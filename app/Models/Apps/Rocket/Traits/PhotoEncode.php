<?php


namespace App\Models\Apps\Rocket\Traits;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Image;
use Storage;

trait PhotoEncode
{
    /**
     * @return Image
     */
    protected function imageDriver()
    {
        return new Image();
    }

    /**
     * @return Filesystem
     */
    protected function storageDriver()
    {
        return Storage::disk($this->disk);
    }

    function buildPath($disk = 'local')
    {
        $this->disk = $disk;

        $originalPath = $this->getOriginalPath();

        if($originalPath && $this->date->toDateString() == '2022-02-15') {
            return $originalPath;
        }

        if ($this->disk == 'local' || (!Str::contains($originalPath, "-") && $originalPath)) return $originalPath;

        $date = $this->attributes['date'] ? $this->date : Carbon::now();

        $dateString = $date->format('Ymd');
        $timeString = $date->format('His');
        $side = $this->side;

        return self::BASE_PATH . "$this->vehicle_id/$dateString/$timeString-$side.jpeg";
    }

    function getOriginalPath()
    {
        return $this->attributes['path'] ?? '';
    }

    function getPathAttribute($path)
    {
        $path = $this->buildPath($this->disk);
        $this->path = $path;
        return $path;
    }

    /**
     * @param string $encode
     * @param false $withEffects
     * @param false $withMask
     * @return \Intervention\Image\Image|string|null
     */
    function encode($encode = "webp", $withEffects = false, $withMask = false)
    {
        if ($encode == "url") {
            $effects = $this->effects && $withEffects ? true : "";
            $mask = $withMask ? true : "";
            return config('app.url') . "/api/v2/files/rocket/get-photo?id=$this->id&with-effect=$effects&mask=$mask";
        }

        return $this->getImage($encode, $withEffects, $withMask);
    }

    function processImage(\Intervention\Image\Image $image, $encode = null, $withEffects = false, $withMask = false, $withTitle = false, $withSeating = false)
    {
        $h = $image->height() * 0.85;

        $avBrightness = $this->getAvgLuminance($image->encode('jpeg')->encode('data-url'));

        if ($withEffects && $this->effects && $avBrightness < 18) {

            $brightness = collect($this->effects->brightness)->filter(function ($brightness) use ($avBrightness) {
                $brightness = (object)$brightness;
                $rangeArray = collect($brightness->range);
                return collect(range($rangeArray->first(), $rangeArray->last()))->contains($avBrightness);
            })->first();

            $brightness = $brightness['value'] ?? 0;
            $contrast = $this->effects->contrast;
            $gamma = $this->effects->gamma;
            $sharpen = $this->effects->sharpen;

            if ($avBrightness < 15) {
                $brightness = 20;
                $contrast = 0;
                $gamma = 1;
                $sharpen = 0;
            }

            $image->contrast(intval($contrast ?? 0))->gamma($gamma ?? 0)->brightness(intval($brightness ?? 0))->sharpen(intval($sharpen ?? 0));

            $this->data_properties = collect($this->data_properties)->put('avBrightness', $avBrightness);

            $image->text('Brightness ' . ($brightness ?? '') . " | AV: $avBrightness", 5, $h, function ($font) {
                $font->color('#00ff00');
            })->text('Contrast ' . intval($contrast ?? ''), 5, $h + 10, function ($font) {
                $font->color('#00ff00');
            })->text('Gamma ' . intval($gamma ?? ''), 5, $h + 20, function ($font) {
                $font->color('#00ff00');
            })->text('Sharpen ' . intval($sharpen ?? ''), 5, $h + 30, function ($font) {
                $font->color('#00ff00');
            });
        }


        if ($withTitle) {
            $image->rectangle($image->width() / 2 - 90, 4, $image->width() / 2 + 90, 40, function ($draw) {
                $draw->background('rgba(0, 0, 0, 0.5)');
            });
            $image->text(__('CAMERA') . " $this->side", $image->width() / 2, 20, function ($font) {
                $font->color('#ffff00');
                $font->file(Storage::path('Apps/Rocket/Profiles/Fonts/Serpentine.ttf'));
                $font->size(14);
                $font->align('center');
            });
            $image->text($this->date->toDateTimeString(), $image->width() / 2, 35, function ($font) {
                $font->color('#ffff00');
                $font->file(Storage::path('Apps/Rocket/Profiles/Fonts/Serpentine.ttf'));
                $font->size(14);
                $font->align('center');
            });
        }

        $image = $encode ? $image->encode($encode) : $image;

        if ($withMask) {
            $pathMask = "Apps/Rocket/Profiles/Mask/$this->side-$this->vehicle_id.png";
            $photoDate = $this->date->toDateString();
            $pathMaskDate = "Apps/Rocket/Profiles/Mask/$this->side-$this->vehicle_id-$photoDate.png";

            if (Storage::exists($pathMaskDate)) {
                $mask = Image::make(Storage::path($pathMaskDate));
                $image->insert($mask, 'center');
            }
            elseif (Storage::exists($pathMask)) {
                $mask = Image::make(Storage::path($pathMask));
                $image->insert($mask, 'center');
            }
        }

        $image->text("PCW @ " . Carbon::now()->format('Y') . "", $image->width() / 2, $image->height() - 10, function ($font) {
            $font->color('#7980ff');
            $font->align('center');
            $font->file(Storage::path('Apps/Rocket/Profiles/Fonts/Serpentine.ttf'));
            $font->size(12);
        });

        $profileSeat = $this->vehicle->getProfileSeating($this->side, $this->date->toDateString());
        if ($profileSeat && $withSeating) {
            $countedSeating = request()->get('counted');
            $countedSeating = collect($countedSeating ? explode(',', $countedSeating) : []);
            foreach ($profileSeat->occupation as $zone) {
                $zone = (object)$zone;
                $center = (object)$zone->center;
                $percentWidth = $image->width() / 100;
                $percentHeight = $image->height() / 100;

                $image->rectangle($center->left * $percentWidth - 22, $center->top * $percentHeight - 25, $center->left * $percentWidth + 22, $center->top * $percentHeight + 5, function ($draw) {
                    $draw->background('rgba(0, 0, 0, 0.2)');
                });

                $counted = $countedSeating->contains($zone->number);
                $image->text($zone->number, $center->left * $percentWidth, $center->top * $percentHeight, function ($font) use ($counted) {
                    $font->color($counted ? '#00f9ff' : '#ffff00');
                    $font->file(Storage::path('Apps/Rocket/Profiles/Fonts/Serpentine.ttf'));
                    $font->size(24);
                    $font->align('center');
                });
            }
        }

        return $image;
    }

    /**
     * @param null $encode
     * @param false $withEffects
     * @param false $withMask
     * @param false $withTitle
     * @param bool $withSeating
     * @return \Intervention\Image\Image|null
     */
    function getImage($encode = null, $withEffects = false, $withMask = false, $withTitle = false, $withSeating = false)
    {
        $image = null;

        try {
            if ($this->storageDriver()->exists($this->path)) {
                $file = Image::make($this->storageDriver()->get($this->path));
                if ($this->vehicle_id == 1873 && intval($this->side) === 2
                    && $this->date->toDateString()>='2022-08-02' && $this->date->toDateString()<='2022-08-10') { // Corrige el giro de la c?mara vh 02 Montebello
                    $file = $file->rotate(180);
                }
                $image = $this->processImage($file, $encode, $withEffects, $withMask, $withTitle, $withSeating);
            }
        } catch (FileNotFoundException $e) {

        }

        return $image;
    }

    function getAvgLuminance($filename, $num_samples = 30)
    {

        // needs a mimetype check
        $img = imagecreatefromjpeg($filename);
//        $img = $filename;

        $width = imagesx($img);
        $height = imagesy($img);

        $x_step = intval($width / $num_samples);
        $y_step = intval($height / $num_samples);

        $total_lum = 0;
        $sample_no = 1;

        for ($x = 0; $x < $width; $x += $x_step) {
            for ($y = 0; $y < $height; $y += $y_step) {

                $rgb = imagecolorat($img, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                // choose a simple luminance formula from here
                // http://stackoverflow.com/questions/596216/formula-to-determine-brightness-of-rgb-color
                $lum = ($r + $r + $b + $g + $g + $g) / 6;

                $total_lum += $lum;
                $sample_no++;
            }
        }

        // work out the average
        $avg_lum = $total_lum / $sample_no;

        return intval(($avg_lum / 255) * 100);
    }
}