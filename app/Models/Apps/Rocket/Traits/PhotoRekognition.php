<?php


namespace App\Models\Apps\Rocket\Traits;


use App\Models\Company\Company;
use App\Services\AWS\RekognitionService;
use Image;
use Storage;

trait PhotoRekognition
{
    /**
     * @param false $force
     * @param null $type
     */
    public function processRekognition($force = false, $type = null)
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

        if (isset($config->photo)) {
            $this->effects = $config->photo->effects;
        }

        $image = $this->getImage('png', true, true);
        $image = Image::make($image)->encode('png'); // It's necessary because image has a Mask

        $column = "data_$type";

//        if ($this->dispatchRegister && $this->dispatchRegister->isActive() || true) {
        if ($this->vehicle->company_id != 2 && $this->dispatchRegister && $this->dispatchRegister->isActive() || $this->vehicle->company_id == 41) {
            $rekognition = new RekognitionService();
            $this->$column = $rekognition->sefFile($image)->process($type);
        }
    }
}
