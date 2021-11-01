<?php

namespace App\Services\Apps\Rocket\Video;

use App\Models\Apps\Rocket\Photo;
use App\Models\Vehicles\Vehicle;
use Illuminate\Support\Collection;
use Storage;

class VideoService
{
    /**
     * @var Vehicle
     */
    private $vehicle;

    /**
     * @var string
     */
    private $date;

    /**
     * @var string
     */
    private $formattedDate;

    /**
     * @var string
     */
    private $path;

    function for(Vehicle $vehicle, $date)
    {
        $this->vehicle = $vehicle;
        $this->date = $date;
        $this->formattedDate = str_replace('-', '', $this->date);

        $this->path = "Apps/Rocket/Photos/" . $this->vehicle->id . "/$this->formattedDate";

        return $this;
    }

    function downloadPhotos()
    {
        $localPath = Storage::disk('local')->path($this->path);

        return shell_exec("aws s3 sync s3://pcw-mov-storage/$this->path/ $localPath/");
    }

    function getPhotos(): Collection
    {
        return Photo::whereDate('date', $this->date)->whereVehicleId($this->vehicle->id)->get();
    }

    function processPhotos()
    {
        $this->getPhotos()->each(function (Photo $photo) {
            $photo->disk = 'local';
            $image = $photo->getImage('jpeg', false, false, true);
            Storage::disk('local')->put($photo->path, $image);
        });
    }

    function processVideo()
    {
        $localPath = Storage::disk('local')->path($this->path);
        shell_exec("cd $localPath && ffmpeg -y -framerate 10 -pattern_type glob -i '*.jpeg' -c:v libx264 -r 30 video.mp4");
    }
}