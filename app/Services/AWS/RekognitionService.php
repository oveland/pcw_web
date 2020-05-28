<?php


namespace App\Services\AWS;


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
            'profile' => 'pcw-rekognition', // Profile configured on ~/.aws/credentials file for a user with access and Rekognition permissions
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

    public function faces()
    {
        $result = $this->rekognition->detectFaces(array(
                'Image' => array(
                    'Bytes' => $this->file,
                ),
                'Attributes' => array('ALL')
            )
        );

        return collect($result);
    }

    /**
     * @return object
     */
    public function person()
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
     * @param $data
     * @return object
     */
    private function castPersonResponse($data)
    {
        $data = (object)collect($data->get('Labels'))->where('Name', 'Person')->first();

        if ($data && isset($data->Instances)) {
            $count = count($data->Instances);

            $draws = [];

            foreach ($data->Instances as $person) {
                $boundingBox = $person['BoundingBox'];
                $draws[] = (object)[
                    'box' => (object)[
                        'width' => $boundingBox['Width'] * 100,
                        'height' => $boundingBox['Height'] * 100,
                        'left' => $boundingBox['Left'] * 100,
                        'top' => $boundingBox['Top'] * 100,
                    ],
                    'confidence' => floatval(number_format($person['Confidence'], 1)),
                ];
            }

            $data = (object)[
                'name' => Str::lower($data->Name),
                'confidence' => floatval(number_format($data->Confidence * 100, 1)),
                'draws' => $draws,
                'count' => $count,
            ];
        }

        return $data;
    }
}