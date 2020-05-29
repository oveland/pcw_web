<?php


namespace App\Services\AWS;


use Aws\Credentials\Credentials;
use Aws\Rekognition\RekognitionClient;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Str;
use Storage;

class RekognitionService
{
    public $file;

    private $rekognition;

    public function __construct()
    {
        $options = [
            'credentials' => new Credentials(config('aws.credentials.rekognition.key'), config('aws.credentials.rekognition.secret')),
//            'profile' => 'pcw-rekognition', // Profile configured on ~/.aws/credentials file for a user with access and Rekognition permissions
            'region' => 'us-west-2',
            'version' => 'latest'
        ];

        $this->rekognition = new RekognitionClient($options);
    }

    /**
     * @param $path
     * @return RekognitionService
     * @throws FileNotFoundException
     */
    public function sefFile($path)
    {
        $this->file = Storage::disk('local')->get($path);

        return $this;
    }

    public function process($type = 'persons')
    {
        switch ($type) {
            case 'persons':
                return $this->persons();
                break;
            case 'faces':
                return $this->faces();
                break;
            default:
                return null;
                break;
        }
    }

    public function faces()
    {
        $result = $this->rekognition->detectFaces(array(
                'Image' => array(
                    'Bytes' => $this->file,
                ),
                'Attributes' => array('ALL')
            )
        );

        return $this->castFacesResponse($result);
    }

    /**
     * @return object
     */
    public function persons()
    {
        $result = collect($this->rekognition->detectLabels(array(
                'Image' => array(
                    'Bytes' => $this->file,
                ),
                'Attributes' => array('ALL')
            )
        ));

        return $this->castPersonResponse($result);
    }

    /**
     * @param $result
     * @return object
     */
    private function castFacesResponse($result)
    {
        $faces = collect($result)->get('FaceDetails');

        $draws = [];
        $count = count($faces);

        foreach ($faces as $face) {
            $draws[] = $this->fillBox($face);
        }

        return (object)[
            'type' => 'faces',
            'draws' => $draws,
            'count' => $count,
        ];
    }

    /**
     * @param $result
     * @return object
     */
    private function castPersonResponse($result)
    {
        $persons = (object)collect($result->get('Labels'))->where('Name', 'Person')->first();

        $count = 0;
        $draws = [];

        if (isset($persons->Instances)) {
            $count = count($persons->Instances);
            foreach ($persons->Instances as $person) {
                $draws[] = $this->fillBox($person);
            }
        }

        return (object)[
            'type' => 'persons',
            'draws' => $draws,
            'count' => $count,
        ];;
    }

    /**
     * @param array $data
     * @return object
     */
    private function fillBox($data)
    {
        $boundingBox = $data['BoundingBox'];

        $width = $boundingBox['Width'] * 100;
        $height = $boundingBox['Height'] * 100;
        $left = $boundingBox['Left'] * 100;
        $top = $boundingBox['Top'] * 100;

        return (object)[
            'box' => (object)[
                'width' => $width,
                'height' => $height,
                'left' => $left,
                'top' => $top,
                'center' => (object)[
                    'left' => $left + $width / 2,
                    'top' => $top + $height / 2,
                ],
            ],
            'confidence' => floatval(number_format($data['Confidence'], 1)),
        ];
    }
}