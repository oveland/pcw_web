<?php


namespace App\Models\Apps\Rocket\Traits;

use File;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Carbon;
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

    public function buildPath($disk = 'local')
    {
        $this->disk = $disk;

        if ($disk == 'local' || $this->disk == 'local') return $this->getOriginalPath();

        $date = $this->attributes['date'] ? $this->date : Carbon::now();

        $dateString = $date->format('Ymd');
        $timeString = $date->format('His');

        return self::BASE_PATH . "$this->vehicle_id/$dateString/$timeString.jpeg";
    }

    public function getOriginalPath()
    {
        return $this->attributes['path'];
    }

    public function getPathAttribute($path)
    {
        $path = $this->buildPath($this->disk);
        $this->path = $path;
        return $path;
    }

    /**
     * @param string $encode
     * @param bool $withEffects
     * @return Image|string
     */
    public function encode($encode = "webp", $withEffects = false)
    {
        if ($encode == "url") {
            return config('app.url') . "/api/v2/files/rocket/get-photo?id=$this->id" . ($this->effects && $withEffects ? "&with-effect=true" : "");
        }

        return $this->getImage($encode, $withEffects);
    }

    /**
     * @param null $encode
     * @param bool $withEffects
     * @return \Intervention\Image\Image|null
     */
    public function getImage($encode = null, $withEffects = false)
    {
        $image = null;

        try {
//            $image = Image::make(File::get('img/image-404.jpg'))->resize(300, 300);
            if ($this->storageDriver()->exists($this->path)) {
                $image = Image::make($this->storageDriver()->get($this->path));

                $h = $image->height() * 0.85;

                if ($withEffects && $this->effects && false) {
                    $avBrightness = $this->getAvgLuminance($image->encode('jpeg')->encode('data-url'));

                    $brightness = collect($this->effects->brightness)->filter(function ($brightness) use ($avBrightness) {
                        $brightness = (object)$brightness;
                        $rangeArray = collect($brightness->range);
                        return collect(range($rangeArray->first(), $rangeArray->last()))->contains($avBrightness);
                    })->first();

                    $brightness = isset($brightness['value']) ? $brightness['value'] : 0;
                    $contrast = $this->effects->contrast;
                    $gamma = $this->effects->gamma;
                    $sharpen = $this->effects->sharpen;

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

                $image->text('PCW | ' . Carbon::now()->format('Y'), 5, $h + 45, function ($font) {
                    $font->color('#00ff00');
                });
            }

//            $this->save();
            $image = $encode ? $image->encode($encode) : $image;
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