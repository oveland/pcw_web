<?php

namespace App\Models\Apps\Rocket\Traits;

use App;
use App\Services\Recognition\Recognition;

trait PhotoRekognition
{
    /**
     * @var Recognition
     */
    protected $recognitionService;

    /**
     * @param false $force
     * @param null $type
     */
    public function processRekognition($force = false, $type = null, $save = false)
    {
        $type = $this->getType($type);
        $this->rekognition = $type;

        if ($force || collect($this->data_persons)->isEmpty()) {
            $vehicle = $this->vehicle;
            if ($this->path && $vehicle) {
                switch ($type) {
                    case 'persons_and_faces':
                        $this->process('persons');
                        $this->process('faces');
                        break;
                    default:
                        $this->process($type);
                        break;
                }
            }
        }

        if ($save) {
            $this->save();
            $this->refresh();
        }
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
        $config = $this->photoRekognitionService($type)->config;
        if (isset($config->photo)) $this->effects = $config->photo->effects;

        if ($this->canProcessRecognition()) {
//            $this->recognitionService = app('recognition.aws');
            $this->recognitionService = app('recognition.opencv');

            $column = "data_$type";
            $this->$column = $this->recognitionService->setPhoto($this)->process($type);
        }
    }

    private function canProcessRecognition()
    {
        return $this->vehicle->company->canPhotoRecognition()
            && $this->dispatchRegister
            && $this->dispatchRegister->isActive();
    }
}
