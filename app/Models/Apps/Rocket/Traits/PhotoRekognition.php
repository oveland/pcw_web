<?php


namespace App\Models\Apps\Rocket\Traits;


use App\Services\AWS\RekognitionService;

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
        $rekognition = new RekognitionService();

        $config = $this->photoRekognitionService($type)->config;

        $this->effects = $config->photo->effects;
        $image = $this->getImage('png', true);

        $column = "data_$type";
        $this->$column = $rekognition->sefFile($image)->process($type);
    }
}