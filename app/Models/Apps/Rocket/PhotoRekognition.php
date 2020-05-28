<?php


namespace App\Models\Apps\Rocket;


use App\Services\AWS\RekognitionService;

trait PhotoRekognition
{
    public function processRekognition()
    {
        if (collect($this->data)->isEmpty()) {
            $rekognition = new RekognitionService();
            $this->data = $rekognition->sefFile($this->path)->person();
        }
    }
}