<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 17/06/2018
 * Time: 11:08 PM
 */

namespace App\Services\API\Apps;

use App\Models\Apps\Rocket\Battery;
use App\Models\Apps\Rocket\CurrentBattery;
use App\Models\Vehicles\CurrentLocation;
use App\Models\Vehicles\Vehicle;
use App\Services\API\Apps\Contracts\APIAppsInterface;
use App\Services\API\Apps\Contracts\APIFilesInterface;
use App\Services\Apps\Rocket\Photos\PhotoService;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Storage;
use Validator;

class APIRocketService implements APIAppsInterface
{
    /**
     * @var Request | Collection
     */
    private $request;

    private $service;

    /**
     * @var Vehicle
     */
    private $vehicle;

    private $currentLocation;

    private $photoService;

    public function __construct($service)
    {
        $this->request = request();
        $this->service = $service ?? $this->request->get('action');
        $this->vehicle = Vehicle::where('plate', $this->request->get('vehicle'))->first();
        $this->vehicle = $this->vehicle ? $this->vehicle : Vehicle::find($this->request->get('vehicle'));
        if ($this->vehicle) $this->currentLocation = CurrentLocation::findByVehicle($this->vehicle);

        $this->photoService = new PhotoService();
    }

    /**
     * @return JsonResponse
     * @throws FileNotFoundException
     */
    public function serve(): JsonResponse
    {
        if ($this->vehicle) {
            switch ($this->service) {
                case 'save-photo':
                    return $this->savePhoto();
                    break;

                case 'event':
                    return $this->event();
                    break;

                case 'save-battery':
                    return $this->saveBattery();
                    break;

                case 'log':
                    return $this->log();
                    break;

                default:
                    return response()->json([
                        'success' => false,
                        'message' => __('Service not found')
                    ]);
                    break;
            }
        }
        return response()->json([
            'success' => false,
            'message' => 'Vehicle not found'
        ]);
    }

    /**
     * @return JsonResponse
     * @throws FileNotFoundException
     */
    public function savePhoto()
    {
        $process = $this->photoService->saveImageData($this->vehicle, $this->request->all());
        if ($process->response->success) {
            $photo = $process->photo;
            Storage::disk('local')->append('photo.log', $photo->encode('url') . " \n$photo->date\n$photo->side: $photo->type\n" . $this->vehicle->plate . ":\n");
        } else {
            Storage::disk('local')->append('photo.log', "Error saving photo: " . $process->response->message . ". Data > " . $this->request->except('img')->toJson());
        }

        return response()->json($process->response);
    }

    /**
     * @return JsonResponse
     * @throws FileNotFoundException
     */
    public function event()
    {
        switch ($this->request->get('action')) {
            case 'take-photo':
                return response()->json($this->photoService->takePhoto($this->vehicle, $this->request->get('side'), $this->request->get('quality')));
                break;
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Action for event not found'
                ]);
                break;
        }
    }

    /**
     * @return JsonResponse
     */
    public function saveBattery()
    {
        $success = false;
        $message = "";

        $validator = Validator::make($this->request->all(), [
            'charging' => 'required',
            'date' => 'required',
            'dateChanged' => 'required',
            'level' => 'required',
        ]);

        if ($validator->passes()) {
            $battery = new Battery();
            $battery->fill($this->request->all());
            $battery->vehicle()->associate($this->vehicle);
            $battery->date_changed = $this->request->get('dateChanged');

            Storage::disk('local')->append('battery.log', $this->vehicle->plate . ": $battery->level%, charging: " . ($battery->charging ? "yes" : "no") . ", changed: $battery->date_changed, sent: $battery->date");

            if ($battery->save()) {
                $currentBattery = CurrentBattery::findByVehicle($this->vehicle);
                $currentBattery->fill($this->request->all());
                $currentBattery->date_changed = $battery->date_changed;

                $success = $currentBattery->save();
                if ($success) $message = "Photo saved successfully";
            }
        } else {
            $message = collect($validator->errors())->flatten()->implode(' ');
        }

        return response()->json([
            'success' => $success,
            'message' => $message
        ]);
    }

    public function log()
    {
        $success = false;
        $message = "";

        $validator = Validator::make($this->request->all(), [
            'data' => 'required',
        ]);

        if ($validator->passes()) {
            Storage::disk('local')->append('rocket.log', $this->request->get('data'));
        } else {
            $message = collect($validator->errors())->flatten()->implode(' ');
        }

        return response()->json([
            'success' => $success,
            'message' => $message
        ]);
    }
}