<?php


namespace App\Services\Apps\Rocket\Photos;


use App\Models\Apps\Rocket\ProfileSeat;
use App\Models\Apps\Rocket\Traits\PhotoInterface;
use App\Models\Vehicles\Vehicle;
use Illuminate\Support\Collection;

abstract class PhotoRekognitionService
{
    protected $type = 'persons';

    /**
     * @var Vehicle
     */
    protected $vehicle;

    /**
     * @var PhotoZone
     */
    protected $zoneDetected;

    /**
     * @var object
     */
    public $config;


    /**
     * PhotoRekognitionService constructor.
     * @param Vehicle $vehicle
     * @param PhotoZone $zoneDetected
     */
    function __construct(Vehicle $vehicle, PhotoZone $zoneDetected)
    {
        $this->vehicle = $vehicle;
        $this->zoneDetected = $zoneDetected;

        $this->config = $this->getConfig();
    }

    /**
     * @param $data
     * @return object
     */
    function processRekognition($data)
    {
        $data = (object)$data;
        $drawsProcessed = collect([]);

        if ($data) {
            foreach ($data->draws as $draw) {
                $drawsProcessed->push($this->processDraw($draw, $data->type));
            }

            return (object)[
                'type' => $data->type,
                'draws' => $drawsProcessed->toArray(),
                'persons' => $drawsProcessed->where('count', true)->count(),
            ];
        }
    }

    /**
     * @return Collection|object
     */
    protected function getConfig()
    {
        $config = json_decode(
            json_encode(
                config('rocket.' . $this->vehicle->company_id . '.' . $this->type), JSON_FORCE_OBJECT
            ),
            false);

        return $config;
    }

    /**
     * @param array | object | Collection $data
     * @return object
     */
    protected function processDraw($data, $type)
    {
        $data = (object)$data;

        $configDraw = $this->config->photo->rekognition->draw;

        $confidence = $data->confidence;
        $boundingBox = (object)$data->box;

        $left = $boundingBox->left;
        $top = $boundingBox->top;
        $width = $boundingBox->width;
        $heightOrig = isset($boundingBox->heightOrig) ? $boundingBox->heightOrig : $boundingBox->height;

        $rule = $this->processDrawRule($confidence, $boundingBox);


        $height = $heightOrig;
        if (isset($configDraw->heightFromWidth)) {
            $height = $width * ($rule->largeDetection ? $configDraw->heightFromWidth->ld : $configDraw->heightFromWidth->nd) / 100;
        } elseif (isset($configDraw->heightFromHeightOrig)) {
            $height = $height * ($rule->largeDetection ? $configDraw->heightFromHeightOrig->ld : $configDraw->heightFromHeightOrig->nd) / 100;
        }

        return (object)[
            'type' => $type,
            'box' => (object)[
                'width' => $width,
                'height' => $height,
                'heightOrig' => $heightOrig,
                'largeDetection' => $rule->largeDetection,
                'left' => $left,
                'top' => $top,
                'center' => (object)[
                    'left' => $left + $width / 2,
                    'top' => $top + $height * ($rule->largeDetection ? $configDraw->centerTopFromHeight->ld : $configDraw->centerTopFromHeight->nd) / 100,
                ],
                'centerOrig' => (object)[
                    'left' => $left + $width / 2,
                    'top' => $top + $heightOrig / 2,
                ],
            ],
            'color' => $rule->color ?? 'white',
            'background' => $rule->background,
            'confidence' => $confidence,
            'relationSize' => $rule->relationSize,
            'largeDetection' => $rule->largeDetection,
            'overlap' => $rule->overlap,
            'count' => $rule->count,
        ];
    }

    /**
     * @param $confidence
     * @param $boundingBox > Values are in percent. Relative to photo image size processed with AWS Rekognition
     * @return object
     */
    protected function processDrawRule($confidence, $boundingBox)
    {
        $confidence = intval($confidence);

        $boxZone = $this->processBoxZone($boundingBox);

        $rules = collect($this->config->photo->rekognition->rules);

        $rule = collect($rules->filter(function ($rule) use ($confidence) {
            $rule = (object)$rule;
            return collect($rule->range)->contains($confidence);
        })->first());

        $count = $rule->get('count') && !$boxZone->overlap && $boundingBox->width > 1.5 && $boundingBox->height > 2;

        if(!$count){
            $rule->put('color', 'rgba(255, 50, 55, 0.78)');
        }

        if ($boxZone->overlap) {
            $rule->put('color', 'rgba(3, 168, 255, 0.78)');
        }

        $background = ($count ? $rule->get('background') : 'rgba(137, 138, 135, 0.1)');

        $rule->put('count', $count);
        $rule->put('overlap', $rule->get('count') && $boxZone->overlap);
        $rule->put('background', $background);
        $rule->put('largeDetection', $boxZone->largeDetection);
        $rule->put('relationSize', $boxZone->relationSize);

        return (object)$rule->toArray();
    }

    /**
     * @param $boundingBox > Values are in percent. Relative to photo image size processed with AWS Rekognition
     * @return object
     */
    protected function processBoxZone($boundingBox)
    {
        $boundingBox = (object)$boundingBox;

        $configBox = $this->config->photo->rekognition->box;

        $width = $boundingBox->width;
        $heightOrig = isset($boundingBox->heightOrig) ? $boundingBox->heightOrig : $boundingBox->height;

        $relationSize = $heightOrig / $width;
        $largeDetection = $relationSize >= $configBox->ld || ($boundingBox->top < 45 && $width > 15);

        $overlap = ($heightOrig > $configBox->od->height) && $width > $configBox->od->width || ($relationSize >= $configBox->od->rs && $width > $configBox->od->rsw);

        return (object)compact(['overlap', 'relationSize', 'largeDetection']);
    }

    /**
     * @param PhotoInterface $photo
     * @return object | null
     */
    function processOccupation(PhotoInterface $photo)
    {
        $occupation = $this->getDataOccupation($photo);

        if ($occupation) {
            $profileSeating = ProfileSeat::findByVehicle($this->vehicle);
            $personDraws = collect([]);
            $seatingOccupied = collect([]);

            $count = true;

            foreach ($occupation->draws as $recognition) {
                $recognition = (object)$recognition;
                if (isset($recognition->count)) {
                    if ($recognition->count) {
                        $this->zoneDetected->buildZone($recognition->box);
                        $profileOccupation = $this->zoneDetected->getProfileOccupation($profileSeating);
                        $recognition->profile = $profileOccupation;

                        if ($profileOccupation->seatOccupied) {
                            $seatingOccupied->put($profileOccupation->seatOccupied->number, $profileOccupation->seatOccupied);
                        }

                        $recognition->profileStr = $profileOccupation->seating->pluck('number')->implode(', ');
                    }

                    if ($recognition->overlap) {
                        $count = false;
                    }
                }
                $personDraws[] = $recognition;
            }

            $occupation->draws = $personDraws;
            $occupation->count = $count;
            $occupation->persons = $seatingOccupied->count();
            $occupation->seatingOccupied = $seatingOccupied;
            $occupation->seatingOccupiedStr = $seatingOccupied->keys()->sort()->implode(', ');

            return $occupation;
        }

        return null;
    }

    /**
     * @param PhotoInterface $photo
     * @return object
     */
    protected function getDataOccupation(PhotoInterface $photo)
    {
        $column = "data_$this->type";
        return $photo->$column;
    }
}