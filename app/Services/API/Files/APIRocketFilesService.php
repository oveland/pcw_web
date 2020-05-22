<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 17/06/2018
 * Time: 11:08 PM
 */

namespace App\Services\API\Files;

use App\Models\Apps\Rocket\Photo;
use App\Models\Vehicles\CurrentLocation;
use App\Models\Vehicles\Vehicle;
use App\Services\API\Files\Contracts\APIFilesInterface;
use App\Services\Apps\Rocket\Photos\PhotoService;
use File;
use Illuminate\Http\Request;
use Image;

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

    private $currentLocation;

    /**
     * @var PhotoService
     */
    private $photoService;

    public function __construct($service)
    {
        $this->request = request();
        $this->service = $service ?? $this->request->get('action');
        $this->vehicle = Vehicle::find($this->request->get('vehicle'));
        if ($this->vehicle) $this->currentLocation = CurrentLocation::findByVehicle($this->vehicle);

        $this->photoService = new PhotoService();
    }

    public function serve()
    {
        switch ($this->service) {
            case 'get-last-photo':
                return $this->getLastPhoto();
                break;
            case 'get-photo':
                return $this->getPhoto();
                break;
            default:

                break;
        }
        return null;
    }


    public function getLastPhoto()
    {
        if ($this->vehicle) {
            return $this->photoService->getLastPhoto($this->vehicle);
        } else {
            return Image::make(File::get('img/image-404.jpg'))->resize(300, 300)->response();
        }
    }

    public function getPhoto()
    {
        $photo = Photo::find($this->request->get('id'));

        if ($photo) {
            return $this->photoService->getPhoto($photo);
        } else {
            return Image::make(File::get('img/image-404.jpg'))->resize(300, 300)->response();
        }
    }
}