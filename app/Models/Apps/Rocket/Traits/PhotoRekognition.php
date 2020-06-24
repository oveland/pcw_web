<?php


namespace App\Models\Apps\Rocket\Traits;


use App\Services\AWS\RekognitionService;

trait PhotoRekognition
{
    /**
     * @param bool $force
     * @param string $type
     */
    public function processRekognition($force = false, $type = 'persons')
    {
        if ($force || (collect($this->data)->isEmpty() && !$this->id)) {
            if ($this->path) {
                $this->effects = [
                    'brightness' => 10,
                    'contrast' => 5,
                    'gamma' => 2,
                    'sharpen' => 12
                ];

                $rekognition = new RekognitionService();

                $this->data = $rekognition->sefFile($this->getImage('png', true))->process($type);
                $this->persons = $this->data ? $this->data->count : 0;
            }
        }
    }
}