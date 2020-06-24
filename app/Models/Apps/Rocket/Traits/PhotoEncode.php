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

        if($disk == 'local' || $this->disk == 'local') return $this->getOriginalPath();

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
            return config('app.url') . "/api/v2/files/rocket/get-photo?id=$this->id" . ($this->effects ? "&with-effect=true&t=" . date('H.i.s.u') : "");
        }

        return $this->getImage($encode, $withEffects);
    }

    /**
     * @param null $encode
     * @param bool $withEffects
     * @return Image|null
     */
    public function getImage($encode = null, $withEffects = false)
    {
        try {
            $image = Image::make(File::get('img/image-404.jpg'))
                ->resize(300, 300);

            if ($this->storageDriver()->exists($this->path)) {
                $image = Image::make($this->storageDriver()->get($this->path));
                if ($withEffects && $this->effects) {
                    $image->contrast(intval($this->effects->contrast))
                        ->gamma($this->effects->gamma)
                        ->brightness(10)
                        ->sharpen(intval($this->effects->sharpen));

                    $image->text('Contrast ' . intval($this->effects->contrast), 5, 430, function ($font) {
                        $font->color('#00ff00');
                    })->text('Gamma ' . intval($this->effects->gamma), 5, 440, function ($font) {
                        $font->color('#00ff00');
                    })->text('Sharpen ' . intval($this->effects->sharpen), 5, 460, function ($font) {
                        $font->color('#00ff00');
                    });
                }
            }

            $image->text('Brightness ' . ($this->effects->brightness ?? ''), 5, 450, function ($font) {
                $font->color('#00ff00');
            });

            $image->text('PCW @ ' . Carbon::now()->format('Y'), 5, 475, function ($font) {
                $font->color('#00ff00');
            });

            return $encode ? $image->encode($encode) : $image;
        } catch (FileNotFoundException $e) {
            return null;
        }
    }
}