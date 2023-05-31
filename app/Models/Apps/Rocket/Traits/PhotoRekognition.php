<?php

namespace App\Models\Apps\Rocket\Traits;

use App;
use App\Services\Recognition\Recognition;
use Exception;
use Log;

trait PhotoRekognition
{
    /**
     * @var Recognition
     */
    protected $recognitionService;

    /**
     * @param false $force
     * @param null $type
     * @return boolean
     */
    public function processRekognition($force = false, $type = null, $save = false)
    {
        $success = true;
        $type = $this->getType($type);
        $this->rekognition = $type;

        if ($force || collect($this->data_persons)->isEmpty()) {
            $vehicle = $this->vehicle;
            if ($this->path && $vehicle) {
                switch ($type) {
                    case 'persons_and_faces':
                        $success = $this->process('persons');
                        if ($success) $success = $this->process('faces');
                        break;
                    default:
                        $success = $this->process($type);
                        break;
                }
            }
        }

        if ($save) {
            $this->save();
            $this->refresh();
        }

        return $success;
    }

    /**
     * @param $type
     * @return string
     */
    private function getType($type)
    {
        if (!$type) $type = $this->rekognition;
        if (!$type) $type = 'persons_and_faces';

        return $type;
    }

    /**
     * @param $type
     */
    private function process($type)
    {
        try {
            $config = $this->photoRekognitionService($type)->config;
            if (isset($config->photo)) $this->effects = $config->photo->effects;

            if ($this->canProcessRecognition() && $this->vehicle->company_id != 2) {
//            $this->recognitionService = app('recognition.aws');
                $this->recognitionService = app('recognition.opencv');

                $column = "data_$type";
                $this->$column = $this->recognitionService->setPhoto($this)->process($type);
            }

            $success = true;
            Log::info("__________________________ Recognition OK $type " . $this->vehicle->number);
        } catch (Exception $e) {
            $success = false;
            $vn = $this->vehicle->number;
            Log::info(" ------------------- $vn Photo $this->id ERROR ON processRekognition Error code: " . $e->getCode());
        }

        return $success;
    }

    private function canProcessRecognition()
    {
        return $this->vehicle->company->canPhotoRecognition()
            && $this->dispatchRegister
            && $this->dispatchRegister->isActive();
    }
}
