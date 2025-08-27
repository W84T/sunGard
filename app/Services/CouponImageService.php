<?php

// app/Services/CouponImageService.php
namespace App\Services;

use App\Models\Coupon;
use ArPHP\I18N\Arabic;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class CouponImageService
{
    // consider moving to config/coupon_image.php
    private string $template = 'templates/master_coupon_galal.png';
    private string $fontReg = 'fonts/NotoSansArabic_Condensed-Medium.ttf';
    private string $fontBold = 'fonts/NotoSansArabic_Condensed-Bold.ttf';

    public function render(Coupon $c): string
    {
        $templatePath = public_path($this->template);
        $fontRegPath = storage_path($this->fontReg);
        $fontBoldPath = storage_path($this->fontBold);

        if (!file_exists($templatePath)) return $c->coupon_link ?? '';

        $ar = new Arabic('Glyphs');
        $shape = fn($t) => $ar->utf8Glyphs($t ?? '');

        $im = new ImageManager(new Driver());
        $img = $im->read($templatePath);

        // logo
        if ($c->exhibitionRelation?->logo_address) {
            $abs = storage_path('app/private/' . $c->exhibitionRelation->logo_address);
            if (file_exists($abs)) {
                $logo = $im->read($abs)
                    ->resize(180, 180, fn($c2) => $c2->aspectRatio()
                        ->upsize());
                $img->place($logo, 'top-left', 1000, 70);
            }
        }

        // fields (same as your current ones)
        $fields = [
            ['t' => $shape($c->customer_name),
                'x' => 2085,
                'y' => 382,
                'size' => 36,
                'align' => 'right',
                'color' => '#000',
                'bold' => false],
            ['t' => $shape($c->customer_phone),
                'x' => 2085,
                'y' => 480,
                'size' => 36,
                'align' => 'right',
                'color' => '#333',
                'bold' => false],
            ['t' => $shape($c->car_model),
                'x' => 2085,
                'y' => 570,
                'size' => 32,
                'align' => 'right',
                'color' => '#000',
                'bold' => false],
            ['t' => $shape($c->car_brand),
                'x' => 2085,
                'y' => 666,
                'size' => 32,
                'align' => 'right',
                'color' => '#000',
                'bold' => false],
            ['t' => $shape(($c->plate_number ?? '') . ' - ' . ($c->plate_characters ?? '')),
                'x' => 2085,
                'y' => 763,
                'size' => 34,
                'align' => 'right',
                'color' => '#000',
                'bold' => false],
            ['t' => $shape(__('car_categories.' . $c->car_category)),
                'x' => 2085,
                'y' => 854,
                'size' => 36,
                'align' => 'right',
                'color' => '#000',
                'bold' => false],
            ['t' => $shape($c->agent->name ?? ''),
                'x' => 2085,
                'y' => 947,
                'size' => 36,
                'align' => 'right',
                'color' => '#000',
                'bold' => false],
            ['t' => $shape($c->created_at?->format('d/m/Y - h:i') ?? now()->format('d/m/Y - h:i')),
                'x' => 2085,
                'y' => 1044,
                'size' => 36,
                'align' => 'right',
                'color' => '#000',
                'bold' => false],
            ['t' => $shape((string)($c->exhibitionRelation?->discount ?? '')),
                'x' => 530,
                'y' => 1160,
                'size' => 86,
                'align' => 'right',
                'color' => '#FFF',
                'bold' => false],
            ['t' => $shape(str_pad((string)$c->id, 7, '0', STR_PAD_LEFT)),
                'x' => 300,
                'y' => 350,
                'size' => 42,
                'align' => 'center',
                'color' => '#000',
                'bold' => false],
        ];

        // plans: wrap BEFORE shaping to avoid word order flips
        if (!empty($c->exhibitionRelation?->plans)) {
            $textX = 1210;
            $numX = 1245;
            $y = 460;
            $lh = 55;
            $fs = 36;
            $max = 700;
            foreach ($c->exhibitionRelation->plans as $i => $plan) {
                $raw = (string)($plan['value'] ?? '');
                $lines = $this->wrapLogicalText($raw, $fontRegPath, $fs, $max);
                foreach ($lines as $j => $ln) {
                    if ($j === 0) {
                        $fields[] = ['t' => $ar->utf8Glyphs('.' . ($i + 1)),
                            'x' => $numX,
                            'y' => $y,
                            'size' => $fs,
                            'align' => 'right',
                            'color' => '#000',
                            'bold' => true];
                    }
                    $fields[] = ['t' => $ar->utf8Glyphs($ln),
                        'x' => $textX,
                        'y' => $y,
                        'size' => $fs,
                        'align' => 'right',
                        'color' => '#000',
                        'bold' => false];
                    $y += $lh;
                }
            }
        }

        foreach ($fields as $f) {
            $img->text($f['t'], $f['x'], $f['y'], function ($font) use ($f, $fontRegPath, $fontBoldPath) {
                $font->filename($f['bold'] ? $fontBoldPath : $fontRegPath);
                $font->size($f['size']);
                $font->color($f['color']);
                $font->align($f['align']);
            });
        }

        // write via Storage public disk
        $relative = "generated/coupon_{$c->id}.png";
        $tmp = tempnam(sys_get_temp_dir(), 'coupon_') . '.png';
        $img->save($tmp, quality: 90);
        Storage::disk('public')
            ->put($relative, file_get_contents($tmp));
        @unlink($tmp);

        return $relative;
    }

    // measure logical text; shape later
    private function wrapLogicalText(string $text, string $fontFile, int $fontSize, int $maxWidth): array
    {
        $words = preg_split('/\s+/u', trim($text)) ?: [];
        $lines = [];
        $cur = '';
        foreach ($words as $w) {
            $test = trim($cur === '' ? $w : "$cur $w");
            $box = imagettfbbox($fontSize, 0, $fontFile, $test);
            $width = abs($box[2] - $box[0]);
            if ($width > $maxWidth && $cur !== '') {
                $lines[] = $cur;
                $cur = $w;
            } else {
                $cur = $test;
            }
        }
        if ($cur !== '') $lines[] = $cur;
        return $lines;
    }
}
