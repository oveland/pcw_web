<?php

namespace App\Services\Recognition;

use App\Models\Apps\Rocket\Photo;
use Log;
use GuzzleHttp\Client;

class RecognitionService implements Recognition
{
    private $client;
    private $urlAPI;

    /**
     * @var Photo
     */
    private $photo;

    public function __construct()
    {
        $this->client = new Client();
        $this->urlAPI = config('gps.server.recognitionUrlAPI');
    }

    /**
     * @param Photo $photo
     * @return $this
     */
    function setPhoto(Photo $photo)
    {
        $this->photo = $photo;
        return $this;
    }

    public function process($type = 'persons')
    {
        $client = new Client();
        $url = config('gps.server.recognitionUrlAPI') . "/$type?id=" . $this->photo->id . "&with_mask=1";
        $response = $client->request('GET', $url, ['timeout' => 0]);

//        Log::info("$type response->getStatusCode() = " . $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents());

        return $this->formatResponse($data, $type);
    }

    /**
     * @param object $result
     * @return object
     */
    private function formatResponse($result, $type)
    {
        $draws = collect($result)->map(function ($draw) {
            return $this->fillBox($draw->box, $draw->confidence);
        });

        return (object)[
            'type' => $type,
            'draws' => $draws->toArray()
        ];
    }

    /**
     * @param $box
     * @param null $confidence
     * @return object
     */
    private function fillBox($box, $confidence = null)
    {
        $left = floatval($box[0]);
        $top = floatval($box[1]);
        $width = floatval($box[2]);
        $height = floatval($box[3]);

        return (object)[
            'box' => (object)[
                'width' => $width,
                'height' => $height,
                'left' => $left,
                'top' => $top,
            ],
            'confidence' => floatval($confidence)
        ];
    }
}