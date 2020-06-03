<?php


namespace App\Models\Apps\Rocket;


use App\Services\AWS\RekognitionService;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

trait PhotoRekognition
{
    /**
     * @param bool $force
     * @throws FileNotFoundException
     */
    public function processRekognition($force = false)
    {
        if ($force || (collect($this->data)->isEmpty() && !$this->id)) {
            if ($this->path) {
                $rekognition = new RekognitionService();
                $this->data = $rekognition->sefFile($this->path)->process();
                $this->persons = $this->data ? $this->data->count : 0;
            }
        }
    }
}