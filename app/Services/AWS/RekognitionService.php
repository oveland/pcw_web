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
            case 'custom':
                $data = $this->custom();
                break;
            default:
                return null;
                break;
        }

        $data->count = collect($data->draws)->where('count', true)->count();

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
            if ($draw->count || true) {
                $draws[] = $draw;
            }
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
            if ($draw->count || true) {
                $draws[] = $draw;
            }
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
                if ($draw->count || true) {
                    $draws[] = $draw;
                }
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

        $width = $boundingBox['Width'] * 100;
        $heightOrig = $boundingBox['Height'] * 100;

        $relationSize = $heightOrig / $width;

        $largeDetection = $relationSize > 2.5;

        $height = $width * ($largeDetection ? 0.95 : 1);
        $left = $boundingBox['Left'] * 100;
        $top = $boundingBox['Top'] * 100;

        $confidence = isset($data['Confidence']) ? floatval(number_format($data['Confidence'], 1)) : intval($confidence);

        $rule = $this->getRuleFromConfidence($confidence, $boundingBox);

        return (object)[
            'box' => (object)[
                'width' => $width,
                'height' => $height,
                'heightOrig' => $heightOrig,
                'relationSize' => $relationSize,
                'largeDetection' => $largeDetection,
                'left' => $left,
                'top' => $top,
                'center' => (object)[
                    'left' => $left + $width / 2,
                    'top' => $top + $height / ($largeDetection ? 1.6 : 2.3), // TODO: En Large Detection el alto del centro debe ajustarse de forma dinámica según la relación H/W
                ],
            ],
            'confidence' => $confidence,
            'color' => $rule->color ?? 'white',
            'count' => $rule->count,
            'overlap' => $rule->overlap,
            'background' => $rule->count ? 'rgba(122, 162, 12, 0.1)' : 'rgba(137, 138, 135, 0.1)',
        ];
    }

    /**
     * @param $confidence
     * @param $boundingBox
     * @return mixed
     */
    public function getRuleFromConfidence($confidence, $boundingBox)
    {
        $confidence = intval($confidence);

        $hasOverlap = $this->checkIfOverlap($boundingBox);

        $rules = collect([
            (object)[
                'range' => range(0, 25),
                'color' => 'red',
                'overlap' => $hasOverlap,
                'count' => false
            ],
            (object)[
                'range' => range(25, 50),
                'color' => 'orange',
                'overlap' => $hasOverlap,
                'count' => false
            ],
            (object)[
                'range' => range(50, 70),
                'color' => 'yellow',
                'overlap' => $hasOverlap,
                'count' => false
            ],
            (object)[
                'range' => range(70, 100),
                'color' => '#9bef00',
                'overlap' => $hasOverlap,
                'count' => true && !$hasOverlap,
            ]
        ]);

        return $rules->filter(function ($rule) use ($confidence) {
            return collect($rule->range)->contains($confidence);
        })->first();
    }

    public function checkIfOverlap($boundingBox)
    {
        $width = $boundingBox['Width'] * 100;
        $height = $boundingBox['Height'] * 100;

        return ($height > 60) && $width > 10;
    }
}