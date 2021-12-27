<?php

namespace App\Services\Apps\Rocket\Video;

use App\Models\Apps\Rocket\Photo;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Collection;
use Intervention\Image\Image;
use Log;
use Storage;
use Iman\Streamer\VideoStreamer;

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

        $this->command("mkdir -p $this->localPath");
        $this->command("mkdir -p $this->videoPath");

        $this->command("chmod -R 777 $this->localPath", true);
        $this->command("chmod -R 777 $this->videoPath", true);
//        $this->command("chown -R nobody:nogroup $this->localPath");

        return $this;
    }

    function downloadPhotos()
    {
        $initial = Carbon::now();
        $this->log("    Sync photos from aws");
        $this->command("aws s3 sync s3://pcw-mov-storage/$this->folder $this->localPath");
        $this->command("chmod -R 777 $this->localPath", true);

        $this->log("    Sync aws in " . Carbon::now()->from($initial));

//        $this->command("chown -R nobody:nogroup $this->localPath");
    }

    function getPhotos(): Collection
    {
        $this->log("    Process photos vehicle = " . $this->vehicle->id . " and date = $this->date");
        return Photo::whereDate('date', $this->date)->whereVehicleId($this->vehicle->id)->get();
    }

    function processPhotos()
    {
        $photos = $this->getPhotos();

        $this->log("    Process " . $photos->count() . " photos");

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
        $videoPath = $this->folder . "video/" . $this->videoName;

        if (Carbon::createFromFormat('Y-m-d', $this->date)->isToday() || true || !Storage::exists($videoPath)) {
            $this->downloadPhotos();
            $totalPhotos = $this->processPhotos();

            if ($totalPhotos) {
                $this->log("    *** Making video");
                $this->command("cd $this->localPath && ffmpeg -y -framerate 2 -pattern_type glob -i '*.jpeg' -c:v libx264 -movflags +faststart $this->videoName");
                $this->command("cd $this->localPath && mv $this->videoName $this->videoPath");
                $this->command("chmod -R 777 $this->videoPath", true);
            } else {
                $videoPath = '404.mp4';
            }
        }

        return $videoPath;
    }

    /**
     * @throws FileNotFoundException?
     */
    function getVideo($process = true)
    {
        $videoPath = $this->folder . "video/" . $this->videoName;
        if ($process) {
            $videoPath = $this->processVideo();
        }

        if (!Storage::exists($videoPath)) {
            $videoPath = '404.mp4';
        }

        VideoStreamer::streamFile(Storage::path($videoPath));
    }

    function log($message)
    {
        Log::info($message);
    }

    function command($command, $sudo = false)
    {
        $command = ($sudo ? "sudo " : "") . explode('sudo', $command)[0];
        $response = shell_exec("$command");
        $this->log("        Run: $command | $response");

        return $response;
    }
}