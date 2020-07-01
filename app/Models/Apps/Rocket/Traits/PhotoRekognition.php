<?php


namespace App\Models\Apps\Rocket\Traits;


use App\Services\AWS\RekognitionService;

trait PhotoRekognition
{
    /**
     * @param bool $force
     * @param string $type
     */
    public function processRekognition($force = false, $type = null)
    {
        $type = $this->getType($type);
        $this->rekognition = $type;


        if ($force || collect($this->data_persons)->isEmpty()) {
            $vehicle = $this->vehicle;
            if ($this->path && $vehicle) {
                $rekognition = new RekognitionService();

                switch ($type) {
                    case 'persons_and_faces':
                        $config = $this->photoRekognitionService('faces')->config;
                        $this->effects = $config->photo->effects;
                        $data = $rekognition->sefFile($this->getImage('png', true))->process($type);
                        $this->data_faces = $data->faces;

                        $config = $this->photoRekognitionService('persons')->config;
                        $this->effects = $config->photo->effects;
                        $data = $rekognition->sefFile($this->getImage('png', true))->process($type);
                        $this->data_persons = $data->persons;
                        break;
                    default:
                        $config = $this->photoRekognitionService($type)->config;
                        $this->effects = $config->photo->effects;
                        $data = $rekognition->sefFile($this->getImage('png', true))->process($type);
                        $column = "data_$type";
                        $this->$column = $data;
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
}