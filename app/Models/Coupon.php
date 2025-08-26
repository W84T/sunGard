<?php

namespace App\Models;

use App\Status;
use ArPHP\I18N\Arabic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Overtrue\LaravelVersionable\Versionable;
use Overtrue\LaravelVersionable\VersionStrategy;
use Spatie\Permission\Traits\HasRoles;

class Coupon extends Model
{
    use SoftDeletes, HasRoles, Versionable;

    protected $guarded = [];

    protected $versionable = [
        'agent_id',
        'branch_id',
        'exhibition_id',
        'employee_id',
        'sungard_branch_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'car_model',
        'car_brand',
        'plate_number',
        'plate_characters',
        'car_category',
        'is_confirmed',
        'reserved_date',
    ];

    protected $versionStrategy = VersionStrategy::SNAPSHOT;

    protected $casts = [
        'status' => Status::class,
        'reserved_date' => 'datetime',
        'reached_at' => 'datetime',
        'is_confirmed' => 'bool',
        'plans' => 'array', // ✅ cast JSON automatically
    ];

    /*
    |--------------------------------------------------------------------------
    | Booted Events
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        static::created(fn($coupon) => $coupon->generateCouponImage());

        static::updating(function ($coupon) {
            $originalStatus = $coupon->getOriginal('status');
            $newStatus = $coupon->status instanceof Status
                ? $coupon->status
                : Status::tryFrom((int)$coupon->status);

            $originalStatusEnum = $originalStatus instanceof Status
                ? $originalStatus
                : Status::tryFrom((int)$originalStatus);

            if ($originalStatusEnum?->isReserved() && $newStatus?->value !== $originalStatusEnum?->value) {
                $coupon->reached_at = now();
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Coupon Image Generation
    |--------------------------------------------------------------------------
    */
    public function generateCouponImage(): void
    {
        $templatePath = public_path('templates/master_coupon_galal.png');
        $arabicFont = storage_path('fonts/NotoSansArabic_Condensed-Medium.ttf');
        $arabicBoldFont = storage_path('fonts/NotoSansArabic_Condensed-Bold.ttf');

        if (!file_exists($templatePath)) {
            return;
        }

        // Arabic glyph shaping
        $ar = new Arabic('Glyphs');
        $prepareText = function ($text) use ($ar) {
            return $ar->utf8Glyphs($text ?? '');
        };

        // ✅ GD driver
        $manager = new ImageManager(new Driver());
        $img = $manager->read($templatePath);

        // --- Exhibition Logo
        if ($this->exhibitionRelation?->logo_address) {
            $absoluteLogoPath = storage_path('app/private/' . $this->exhibitionRelation->logo_address);
            if (file_exists($absoluteLogoPath)) {
                $logo = $manager->read($absoluteLogoPath)
                    ->resize(180, 180, function ($c) {
                        $c->aspectRatio();
                        $c->upsize();
                    });
                $img->place($logo, 'top-left', 1000, 70);
            }
        }

        // --- Base fields
        $fields = [
            ['text' => $prepareText($this->customer_name),  'x' => 2085, 'y' => 382,  'size' => 36, 'align' => 'right', 'color' => '#000000', 'weight' => 'normal'],
            ['text' => $prepareText($this->customer_phone), 'x' => 2085, 'y' => 480,  'size' => 36, 'align' => 'right', 'color' => '#333333', 'weight' => 'bold'],
            ['text' => $prepareText($this->car_model),      'x' => 2085, 'y' => 570,  'size' => 32, 'align' => 'right', 'color' => '#000000', 'weight' => 'normal'],
            ['text' => $prepareText($this->car_brand),      'x' => 2085, 'y' => 666,  'size' => 32, 'align' => 'right', 'color' => '#000000', 'weight' => 'normal'],
            ['text' => $prepareText(($this->plate_number ?? '') . ' - ' . ($this->plate_characters ?? '')),
                'x' => 2085, 'y' => 763,  'size' => 34, 'align' => 'right', 'color' => '#000000', 'weight' => 'normal'],
            ['text' => $prepareText(__('car_categories.' . $this->car_category)),
                'x' => 2085, 'y' => 854,  'size' => 36, 'align' => 'right', 'color' => '#000000', 'weight' => 'normal'],
            ['text' => $prepareText($this->agent->name ?? ''),
                'x' => 2085, 'y' => 947,  'size' => 36, 'align' => 'right', 'color' => '#000000', 'weight' => 'normal'],
            ['text' => $prepareText($this->created_at?->format('d/m/Y - h:i') ?? now()->toDateString()),
                'x' => 2085, 'y' => 1044, 'size' => 36, 'align' => 'right', 'color' => '#000000', 'weight' => 'normal'],
            ['text' => $prepareText($this->exhibitionRelation?->discount),
                'x' => 530,  'y' => 1160, 'size' => 86, 'align' => 'right', 'color' => '#FFF',     'weight' => 'bold'],
            ['text' => $prepareText(str_pad($this->id, 7, '0', STR_PAD_LEFT)),
                'x' => 300,  'y' => 350,  'size' => 42, 'align' => 'center','color' => '#000000', 'weight' => 'bold'],
        ];

        // --- Plans (with wrapping)
        if (!empty($this->exhibitionRelation?->plans)) {
            $textX = 1210;   // right-aligned text
            $numX  = 1245;   // number column
            $cursorY = 460;  // start Y position
            $lineHeight = 55;
            $fontSize = 36;
            $maxWidth = 650; // max width for text lines

            foreach ($this->exhibitionRelation->plans as $i => $plan) {
                $number = '.' . $prepareText(($i + 1));
                $text   = $prepareText($plan['value'] ?? '');

                // Break plan text into wrapped lines
                $lines = $this->wrapArabicText($text, $arabicFont, $fontSize, $maxWidth);

                foreach ($lines as $j => $line) {
                    // number only on first line
                    if ($j === 0) {
                        $fields[] = [
                            'text'  => $number,
                            'x'     => $numX,
                            'y'     => $cursorY,
                            'size'  => $fontSize,
                            'align' => 'right',
                            'color' => '#000000',
                            'weight'=> 'bold',
                        ];
                    }

                    $fields[] = [
                        'text'  => $line,
                        'x'     => $textX,
                        'y'     => $cursorY,
                        'size'  => $fontSize,
                        'align' => 'right',
                        'color' => '#000000',
                        'weight'=> 'normal',
                    ];

                    $cursorY += $lineHeight;
                }
            }
        }

        // --- Draw all fields (always Arabic fonts)
        foreach ($fields as $field) {
            $img->text($field['text'], $field['x'], $field['y'], function ($font) use ($field, $arabicFont, $arabicBoldFont) {
                $font->filename($field['weight'] === 'bold' ? $arabicBoldFont : $arabicFont);
                $font->size($field['size']);
                $font->color($field['color']);
                $font->align($field['align']);
            });
        }

        // --- Save
        $outputDir = public_path('generated');
        if (!file_exists($outputDir)) mkdir($outputDir, 0755, true);

        $outputPath = $outputDir . "/coupon_{$this->id}.png";
        $img->save($outputPath);

        $this->updateQuietly(['coupon_link' => "generated/coupon_{$this->id}.png"]);
    }

    /**
     * Wrap Arabic text to fit max width
     */
    protected function wrapArabicText(string $text, string $fontFile, int $fontSize, int $maxWidth): array
    {
        $words = explode(' ', $text);
        $lines = [];
        $currentLine = '';

        foreach ($words as $word) {
            $testLine = trim($currentLine . ' ' . $word);
            $box = imagettfbbox($fontSize, 0, $fontFile, $testLine);
            $lineWidth = abs($box[2] - $box[0]);

            if ($lineWidth > $maxWidth && $currentLine !== '') {
                $lines[] = $currentLine;
                $currentLine = $word;
            } else {
                $currentLine = $testLine;
            }
        }

        if ($currentLine !== '') {
            $lines[] = $currentLine;
        }

        return $lines;
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function branchRelation(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function exhibitionRelation(): BelongsTo
    {
        return $this->belongsTo(Exhibition::class, 'exhibition_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function sungard(): BelongsTo
    {
        return $this->belongsTo(SungardBranches::class, 'sungard_branch_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'coupon_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */
    public function getCarPlateAttribute(): string
    {
        return strtoupper($this->plate_characters) . '-' . $this->plate_number;
    }
}
