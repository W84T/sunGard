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
        require_once base_path('vendor/khaled.alshamaa/ar-php/src/Arabic.php');

        $templatePath = public_path('templates/coupon_template.jpg');
        $arabicFont = public_path('fonts/NotoNaskhArabic-VariableFont_wght.ttf');
        $englishFont = public_path('fonts/Roboto-VariableFont_wdth,wght.ttf');

        if (!file_exists($templatePath)) {
            return;
        }

        $ar = new Arabic('Glyphs');

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

        $manager = new ImageManager(new Driver);
        $img = $manager->read($templatePath);

        $fields = [
            [$this->customer_name, 200, 150, 32],
            [$this->customer_phone, 200, 200, 28],
            [$this->car_model, 200, 250, 28],
            [$this->plate_number, 200, 300, 28],
        ];

        foreach ($fields as [$text, $x, $y, $size]) {
            $data = $prepareText($text);

            $img->text($data['text'], $x, $y, function ($font) use ($data, $arabicFont, $englishFont, $size) {
                $font->filename($data['isArabic'] ? $arabicFont : $englishFont);
                $font->size($size);
                $font->color('#000000');
                $font->align($data['isArabic'] ? 'right' : 'left');
            });
        }

        $outputDir = public_path('generated');
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $outputPath = $outputDir . "/coupon_{$this->id}.jpg";
        $img->save($outputPath);

        $this->updateQuietly([
            'coupon_link' => "generated/coupon_{$this->id}.jpg",
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
