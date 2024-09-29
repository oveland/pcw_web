<?php

namespace App\Services\AWS;

use App\Models\Apps\Rocket\Photo;
use App\Services\Recognition\Recognition;
use Aws\Credentials\Credentials;
use Aws\Rekognition\RekognitionClient;
use Illuminate\Support\Collection;
use Image;

class RekognitionService implements Recognition
{
    public $file;

    private $rekognition;

    function __construct()
    {
        $options = [
            'credentials' => new Credentials(config('aws.credentials.rekognition.key'), config('aws.credentials.rekognition.secret')),
            'region' => 'us-west-2',
            'version' => 'latest'
        ];

        $this->rekognition = new RekognitionClient($options);
    }

    function sefFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @param Photo $photo
     * @return $this
     */
    function setPhoto(Photo $photo)
    {
        $file = Image::make($photo->getImage('png', true, true))->encode('png');

        $this->sefFile($file);

        return $this;
    }

    function process($type = 'persons')
    {
        switch ($type) {
            case 'asociate':
                return $this->asociate();
                break;
            case 'persons':
                return $this->persons();
                break;
            case 'faces':
                return $this->faces();
                break;
                break;
            default:
                return null;
                break;
        }

        return $data;
    }
    function asociate()
    {
        var_dump('se entra aqui');
        $startTime = '2024-08-28 11:32:40';
        $endTime = '2024-08-28 11:42:05';
        $dispatchRegisterId = 3483989;

        // Obtener los paths de las imágenes de S3 desde la base de datos
        $photoPaths = \DB::table('app_photos')
            ->where('dispatch_register_id', $dispatchRegisterId)
            ->whereBetween('date', [$startTime, $endTime])
            ->pluck('path'); // Paths relativos al bucket de S3

        $collectionId = 'mi-coleccion-de-rostros'; // ID de la colección creada previamente
        $results = collect();

        // Verifica que haya paths
        if ($photoPaths->isEmpty()) {
            return 'No se encontraron imágenes en el rango de tiempo especificado.';
        }
        var_dump('paso aqui');

        foreach ($photoPaths as $path) {
            // Asegúrate de que el path esté correctamente formateado
            try {
                $result = $this->rekognition->indexFaces([
                    'CollectionId' => $collectionId, // Añadir CollectionId aquí
                    'Image' => [
                        'S3Object' => [
                            'Bucket' => 'pcw-mov-storage', // Verifica que sea el bucket correcto
                            'Name' => ltrim($path, '/'), // Asegúrate de que no tenga '/' al inicio
                        ],
                    ],
                    'Attributes' => ['ALL'],
                ]);

                // Almacenar los resultados de la indexación
                $results->push($result);
            } catch (\Exception $e) {
                // Registra cualquier error en los logs
                var_dump($e->getMessage());
                \Log::error('Error al indexar la imagen: ' . $path . ' - ' . $e->getMessage());
            }
        }

        return $results;
    }

    function faces()
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
    function persons()
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
    function custom()
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