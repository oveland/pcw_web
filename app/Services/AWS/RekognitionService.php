<?php

namespace App\Services\AWS;

use Aws\Credentials\Credentials;
use Aws\Rekognition\RekognitionClient;
use Illuminate\Support\Collection;
use Intervention\Image\Image;

class RekognitionService
{
    public $file;

    private $rekognition;

    public function __construct()
    {
        $options = [
            'credentials' => new Credentials(config('aws.credentials.rekognition.key'), config('aws.credentials.rekognition.secret')),
            'region' => 'us-west-2',
            'version' => 'latest'
        ];

        $this->rekognition = new RekognitionClient($options);
    }

    /**
     * @param Image $file
     * @return $this
     */
    public function sefFile($file)
    {
        $this->file = $file;

        return $this;
    }

    public function process($type = 'persons')
    {
        $data = null;
        switch ($type) {
            case 'persons':
                $data = $this->persons();
                break;
            case 'faces':
                $data = $this->faces();
                break;
                break;
            default:
                return null;
                break;
        }

        // $data->count = collect($data->draws)->where('count', true)->count();

        return $data;
    }

    public function faces()
    {
        $result = collect($this->rekognition->detectFaces(array(
                'Image' => array(
                    'Bytes' => $this->file,
                ),
                'Attributes' => array('ALL')
            )
        ));

        return $this->castFacesResponse($result);
    }

    /**
     * @return object
     */
    public function persons()
    {
        $result = collect($this->rekognition->detectLabels([
                'Image' => [
                    'Bytes' => $this->file,
                ],
                'MinConfidence' => 50,
            ]
        ));

        return $this->castPersonResponse($result);
    }

    /**
     * @return object
     */
    public function custom()
    {
        $result = collect($this->rekognition->detectCustomLabels([
                'Image' => [
                    'Bytes' => $this->file,
                ],
                'MaxResults' => 60,
                'MinConfidence' => 10,
                'ProjectVersionArn' => 'arn:aws:rekognition:us-west-2:042371770682:project/Rocket/version/Rocket.2020-06-09T21.58.18/1591757898830'
            ]
        ));

        return $this->castCustomResponse($result);
    }

    /**
     * @param Collection $result
     * @return object
     */
    private function castFacesResponse($result)
    {
        $faces = $result->get('FaceDetails');

        $draws = [];

        foreach ($faces as $face) {
            $draw = $this->fillBox($face);
            $draws[] = $draw;
        }

        return (object)[
            'type' => 'faces',
            'draws' => $draws,
        ];
    }

    /**
     * @param Collection $result
     * @return object
     */
    private function castCustomResponse($result)
    {
        $heads = collect($result->get('CustomLabels'))->where('Name', 'Head');

        $draws = [];

        foreach ($heads as $head) {
            $head = collect($head);
            $data = $head->get('Geometry');
            $confidence = floatval(number_format($head->get('Confidence'), 1));
            $draw = $this->fillBox($data, $confidence);
            $draws[] = $draw;
        }

        return (object)[
            'type' => 'heads',
            'draws' => $draws
        ];
    }

    /**
     * @param Collection $result
     * @return object
     */
    private function castPersonResponse($result)
    {
        $persons = (object)collect($result->get('Labels'))->where('Name', 'Person')->first();

        $draws = [];

        if (isset($persons->Instances)) {
            foreach ($persons->Instances as $person) {
                $draw = $this->fillBox($person);
                $draws[] = $draw;
            }
        }

        return (object)[
            'type' => 'persons',
            'draws' => $draws
        ];
    }

    /**
     * @param array $data
     * @param null $confidence
     * @return object
     */
    private function fillBox($data, $confidence = null)
    {
        $boundingBox = $data['BoundingBox'];

        $left = $boundingBox['Left'] * 100;
        $top = $boundingBox['Top'] * 100;
        $width = $boundingBox['Width'] * 100;
        $height = $boundingBox['Height'] * 100;

        $confidence = isset($data['Confidence']) ? floatval(number_format($data['Confidence'], 1)) : intval($confidence);

        return (object)[
            'box' => (object)[
                'width' => $width,
                'height' => $height,
                'left' => $left,
                'top' => $top,
            ],
            'confidence' => $confidence
        ];
    }
}