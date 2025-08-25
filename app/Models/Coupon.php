<?php

namespace App\Models;

use App\Status;
use ArPHP\I18N\Arabic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Overtrue\LaravelVersionable\Versionable;
use Overtrue\LaravelVersionable\VersionStrategy;
use Spatie\Permission\Traits\HasRoles;

class Coupon extends Model
{
    use SoftDeletes;
    use HasRoles;
    use Versionable;

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
    ];

    //    protected static function booted(): void
    //    {
    //        static::updating(function ($coupon) {
    //            $originalStatus = $coupon->getOriginal('status');
    //            $newStatus = $coupon->status;
    //
    //            // Only act if status is actually changing
    //            if ($originalStatus !== $newStatus) {
    //                $user = Auth::user();
    //
    //                if ($user && $user->roles->contains('slug', 'employee')) {
    //                    $coupon->employee_id= $user->id;
    //                }
    //            }
    //        });
    //    }

    protected static function booted()
    {
        static::created(function ($coupon) {
            $coupon->generateCouponImage();
        });

        static::updating(function ($coupon) {
            $originalStatus = $coupon->getOriginal('status');
            $newStatus = $coupon->status instanceof Status
                ? $coupon->status
                : Status::tryFrom((int)$coupon->status);

            $originalStatusEnum = $originalStatus instanceof Status
                ? $originalStatus
                : Status::tryFrom((int)$originalStatus);

            // If old status was RESERVED and new status is different
            if ($originalStatusEnum?->isReserved() && $newStatus?->value !== $originalStatusEnum?->value) {
                $coupon->reached_at = now();
            }
        });
    }

    public function generateCouponImage(): void
    {
        $templatePath = public_path('templates/master_coupon_galal.png');
        $arabicFont = storage_path('fonts/NotoSansArabic_Condensed-Medium.ttf');
        $englishFont = storage_path('fonts/CascadiaCode-Regular.ttf');

        if (!file_exists($templatePath)) {
            return;
        }

        $ar = new \ArPHP\I18N\Arabic('Glyphs');

        $prepareText = function ($text) use ($ar) {
            if (preg_match('/\p{Arabic}/u', $text)) {
                return [
                    'text' => $ar->utf8Glyphs($text),
                    'isArabic' => true,
                ];
            }
            return [
                'text' => $text,
                'isArabic' => false,
            ];
        };

        $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
        $img = $manager->read($templatePath);

        // Insert exhibition logo if available
        $logoPath = $this->branchRelation?->exhibition?->logo_address;

        if ($logoPath) {
            // If the DB already stores absolute path:
            $absoluteLogoPath = $logoPath;

            // Or if it’s stored relative (like "exhibition_logos/xxx.png"), use:
             $absoluteLogoPath = storage_path('app/private/' . $logoPath);

            if (file_exists($absoluteLogoPath)) {
                $logo = $manager->read($absoluteLogoPath)
                    ->resize(180, 180, function ($constraint) {
                        $constraint->aspectRatio();   // keep proportions
                        $constraint->upsize();        // prevent enlarging if smaller
                    });

                $img->place($logo, 'top-left', 1000, 70);
            } else {
                \Log::warning("Coupon {$this->id} → Logo missing: $absoluteLogoPath");
            }
        }

        // Text fields
        $fields = [
            [$this->customer_name ?? '', 2085, 382, 36, true],
            [$this->customer_phone ?? '', 2085, 480, 36, true],
            [$this->car_model ?? '', 2085, 570, 36, true],
            [$this->car_brand ?? '', 2085, 666, 36, true],
            [$this->plate_number . ' - ' . $this->plate_characters ?? '', 2085, 763, 36, true],
            [__('car_categories.' . $this->car_category) ?? '', 2085, 854, 36, true],
            [$this->agent->name ?? '', 2085, 947, 36, true],
            [$this->created_at?->format('d/m/Y - h:m') ?? now()->toDateString(), 2085, 1044, 36, true],

            // Serial on the left
            [str_pad($this->id, 7, '0', STR_PAD_LEFT) ?? '000123', 300, 350, 36, false],
        ];

        foreach ($fields as [$text, $x, $y, $size, $isRightAligned]) {
            $data = $prepareText($text);

            $img->text($data['text'], $x, $y, function ($font) use ($data, $arabicFont, $englishFont, $size, $isRightAligned) {
                $font->filename($data['isArabic'] ? $arabicFont : $englishFont);
                $font->size($size);
                $font->color('#000000');
                $font->align($isRightAligned ? 'right' : 'center');
            });
        }

        $outputDir = public_path('generated');
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $outputPath = $outputDir . "/coupon_{$this->id}.png";
        $img->save($outputPath);

        $this->updateQuietly([
            'coupon_link' => "generated/coupon_{$this->id}.png",
        ]);
    }



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

    // App\Models\Coupon
    public function sungard(): BelongsTo
    {
        return $this->belongsTo(SungardBranches::class, 'sungard_branch_id');
    }

    public function getCarPlateAttribute()
    {
        return strtoupper($this->plate_characters) . '-' . $this->plate_number;
    }

    public function tickets(): hasMany
    {
        return $this->hasMany(Ticket::class, 'coupon_id');
    }
}
