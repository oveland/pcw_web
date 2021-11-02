<?php

namespace App\Services\Apps\Rocket\Video;

use App\Models\Apps\Rocket\Photo;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Collection;
use Intervention\Image\Image;
use Storage;

class VideoService
{
    /**
     * @var FilesystemAdapter
     */
    private $localDisk;

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
    private $folder;

    /**
     * @var string
     */
    private $localPath;

    /**
     * @var string
     */
    private $videoPath;

    /**
     * @var string
     */
    private $videoName;

    function for(Vehicle $vehicle, $date)
    {
        $this->localDisk = Storage::disk('local');

        $this->vehicle = $vehicle;
        $this->date = $date;

        $formattedDate = str_replace('-', '', $this->date);

        $this->folder = "Apps/Rocket/Photos/" . $this->vehicle->id . "/$formattedDate/";
        $this->localPath = $this->localDisk->path($this->folder);
        $this->videoPath = "$this->localPath" . "video/";

        $this->videoName = "video.mp4";

        shell_exec("mkdir -p $this->videoPath");

        return $this;
    }

    function downloadPhotos()
    {
        return shell_exec("aws s3 sync s3://pcw-mov-storage/$this->folder $this->localPath");
    }

    function getPhotos(): Collection
    {
        return Photo::whereDate('date', $this->date)->whereVehicleId($this->vehicle->id)->get();
    }

    function processPhotos()
    {
        $photos = $this->getPhotos();
        $photos->each(function (Photo $photo) {
            $photo->disk = 'local';
            $image = $photo->getImage('jpeg', false, false, true);

            $this->processImage($photo, $image);
        });

        return $photos->count();
    }

    function processImage(Photo $photo, Image $image = null)
    {
        if ($image) {
//            Storage::put($this->videoPath . $photo->uid, $image);
            Storage::put($photo->path, $image);
        }
    }

    function processVideo()
    {
        shell_exec("cd $this->localPath && ffmpeg -y -framerate 2 -pattern_type glob -i '*.jpeg' -c:v libx264 -b 200K $this->videoName");
        shell_exec("cd $this->localPath && mv $this->videoName $this->videoPath");
    }

    /**
     * @throws FileNotFoundException
     */
    function getVideo()
    {
        $videoPath = $this->folder . "video/" . $this->videoName;

        if (Carbon::createFromFormat('Y-m-d', $this->date)->isToday() || !Storage::exists($videoPath)) {
            $this->downloadPhotos();
            $totalPhotos = $this->processPhotos();

            if ($totalPhotos) {
                $this->processVideo();
            } else {
                $videoPath = '404.mp4';
            }
        }

        $video = Storage::get($videoPath);

        $response = \Response::make($video);
        $response->header('Content-Type', 'video/mp4');

        return $response;
    }

}