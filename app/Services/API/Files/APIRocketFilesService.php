<?php

namespace App\Services\API\Files;

use App\Models\Apps\Rocket\Photo;
use App\Models\Vehicles\Vehicle;
use App\Services\API\Files\Contracts\APIFilesInterface;
use App\Services\Apps\Rocket\Photos\PhotoService;
use App\Services\Apps\Rocket\Video\VideoService;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Intervention\Image\Image;

class APIRocketFilesService implements APIFilesInterface
{
    /**
     * @var Request
     */
    private $request;
    private $service;

    /**
     * @var Vehicle
     */
    private $vehicle;

    /**
     * @var PhotoService
     */
    private $photoService;

    public function __construct($service)
    {
        $this->request = request();

        if ($this->request->get('vehicle') == 'DEM-003') {
            $this->request = collect($this->request->all());
            $this->request->put('vehicle', 'VCK-542');
        }

        $this->service = $service ?? $this->request->get('action');
        $this->vehicle = Vehicle::find($this->request->get('vehicle'));

        $this->photoService = new PhotoService();
    }

    /**
     * @return Image|mixed|null
     */
    public function serve()
    {
        switch ($this->service) {
            case 'get-last-photo':
                return $this->getLastPhoto();
                break;
            case 'get-photo':
                return $this->getPhoto();
            case 'get-video':
                return $this->getVideo();
                break;
            default:

                break;
        }
        return null;
    }

    /**
     * @return Image|mixed
     */
    public function getLastPhoto()
    {
        if ($this->vehicle) {
            return $this->photoService->for($this->vehicle, 0)->getLastPhoto();
        } else {
            return $this->photoService->notFoundImage();
        }
    }

    /**
     * @return Image|mixed
     */
    public function getPhoto()
    {
        $photo = Photo::find($this->request->get('id'));

        if ($photo) {
            return $this->photoService->getFile(
                $photo,
                $this->request->get('encode') ?? 'png',
                $this->request->get('with-effect') ?? $this->request->get('effect'),
                $this->request->get('mask') ?? false,
                $this->request->get('title') ?? false,
            );
        } else {
            return $this->photoService->notFoundImage();
        }
    }

    /**
     * @return mixed|null
     * @throws FileNotFoundException
     */
    public function getVideo()
    {
        $vehicle = Vehicle::find($this->request->get('vehicle'));

        $date = $this->request->get('date');

        if ($vehicle) {
            \Artisan::call("rocket:s3:video --vehicle-plate=$vehicle->plate --date=$date");

            $videoService = new VideoService();
            return $videoService->for($vehicle, $date)->getVideo(false);
        }

        return $this->photoService->notFoundImage();
    }
}
