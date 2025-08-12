<?php

namespace App\Models;

use App\Status;
use ArPHP\I18N\Arabic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Overtrue\LaravelVersionable\Versionable;
use Spatie\Permission\Traits\HasRoles;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;
    use HasRoles;
    use Versionable;

    protected $guarded = [];

    protected $casts = [
        'status'       => Status::class,
        'reserved_date'=> 'datetime',
        'reached_at'   => 'datetime',
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
//        static::created(function ($coupon) {
//            $coupon->generateCouponImage();
//        });
    }


    public function generateCouponImage()
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
                    'isArabic' => true
                ];
            }
            return [
                'text' => $text,
                'isArabic' => false
            ];
        };

        $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
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
            'coupon_link' => "generated/coupon_{$this->id}.jpg"
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
        return $this->belongsTo(SungardBranches ::class, 'sungard_branch_id');
    }


    public function getCarPlateAttribute()
    {
        return strtoupper($this->plate_characters) . '-' .$this->plate_number   ;
    }

    public function tickets(): hasMany
    {
        return $this->hasMany(Ticket::class, 'coupon_id');
    }

}
