<?php


namespace App\Services\Apps\Rocket\Photos;


use App\Models\Apps\Rocket\ConfigProfile;
use App\Models\Apps\Rocket\ProfileSeat;
use App\Models\Apps\Rocket\Traits\PhotoInterface;
use App\Models\Vehicles\Vehicle;
use App\Services\Apps\Rocket\ConfigProfileService;
use Illuminate\Support\Collection;

abstract class
PhotoRekognitionService
{
    protected $type = 'persons';

    /**
     * @var Vehicle
     */
    protected $vehicle;

    /**
     * @var PhotoZone
     */
    public $zoneDetected;

    /**
     * @var object
     */
    public $config;

    /**
     * @var ProfileSeat
     */
    private $profileSeating;


    /**
     * PhotoRekognitionService constructor.
     * @param PhotoZone $zoneDetected
     * @param ProfileSeat $profileSeating
     */
    function __construct(PhotoZone $zoneDetected, ProfileSeat $profileSeating)
    {
        $this->vehicle = $profileSeating->vehicle;
        $this->zoneDetected = $zoneDetected;

        $configService = new ConfigProfileService($this->vehicle);
        $this->config = $configService->type($this->type);
        $this->profileSeating = $profileSeating;
    }

    /**
     * @param $data
     * @return object
     */
    function processRekognition($data)
    {
        $data = (object)$data;
        $drawsProcessed = collect([]);

        if ($data && isset($data->draws)) {
            foreach ($data->draws as $draw) {
                $drawsProcessed->push($this->processDraw($draw, $data->type));
            }

            return (object)[
                'type' => $data->type,
                'draws' => $drawsProcessed->toArray(),
                'persons' => $drawsProcessed->where('count', true)->count(),
            ];
        }

        return null;
    }

    /**
     * @param array | object | Collection $data
     * @param $type
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
            $rangeArray = collect($rule->range);
            return collect(range($rangeArray->first(), $rangeArray->last()))->contains($confidence);
        })->first());

        $count = $rule->get('count') && !$boxZone->overlap;// && $boundingBox->width > 1.5 && $boundingBox->height > 2;

        if (!$count) {
            $rule->put('color', 'rgba(255, 50, 55, 0.78)');
        }

        if ($boxZone->overlap) {
            $rule->put('color', 'rgba(3, 168, 255, 0.78)');
        }

        $background = ($count ? $rule->get('background') : 'rgba(137, 138, 135, 0.1)');

        $rule->put('count', $count && !$boxZone->overlap);
        $rule->put('overlap', $boxZone->overlap);
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
        $height = $boundingBox->height;

        $heightOrig = isset($boundingBox->heightOrig) ? $boundingBox->heightOrig : $height;

        if (isset($boundingBox->center)) {
            $boundingBox->center = (object)$boundingBox->center;
            $centerOrig = (object)(isset($boundingBox->center) ? $boundingBox->center : $boundingBox->centerOrig);
        } else {
            $centerOrig = (object)[
                'left' => $boundingBox->left + $width / 2,
                'top' => $boundingBox->top + $heightOrig / 2,
            ];
        }

        $relationSize = $heightOrig / $width;
        $largeDetection = $relationSize >= $configBox->ld || ($boundingBox->top < 45 && $width > 15);

        $overlap = false;

        if (isset($boundingBox->center)) {
            if ($boundingBox->center->top < 60 || true) {   // For overlaps located on medium screen
                if ($boundingBox->center->left > 30 && $boundingBox->center->left < 60) { // Only overlaps located on center/front bottom screen
                    $overlap = ($heightOrig > 30 && $width > 30);
                }
            }

            if ($boundingBox->center->top < 45) {   // For overlaps located on medium screen
                $overlap = $overlap || ($heightOrig > 30 && $width > 13);
            }

            if ($boundingBox->center->top < 30) { // For overlaps located on top/back screen
                $overlap = $overlap || ($heightOrig > 30 && $width > 10);
            }
        }

        $overlap = false;

        return (object)compact(['overlap', 'relationSize', 'largeDetection']);
    }

    /**
     * @param PhotoInterface $photo
     * @return object
     */
    function processOccupation(PhotoInterface $photo)
    {
        $occupation = $this->getDataOccupation($photo);
        return $this->occupationParams($occupation);
    }

    /**
     * @param $occupation
     * @return object
     */
    function occupationParams($occupation)
    {
        $profileSeating = $this->profileSeating;

        if (!$profileSeating) {
            $profileSeating = $this->vehicle->profile_seating;
            \Log::info("Load ProfileSeat!!");
        }

        $occupation = (object)$occupation;

        $personDraws = collect([]);
        $seatingOccupied = collect([]);

        $count = true;
        $overlap = false;

        if (isset($occupation->draws)) {
            foreach ($occupation->draws as $recognition) {
                $recognition = (object)$recognition;
                if (isset($recognition->count)) {
                    if ($recognition->count) {
                        $this->zoneDetected->buildZone($recognition->box);
                        $this->zoneDetected->setType($recognition->type);
                        $profileOccupation = $this->zoneDetected->getProfileOccupation($profileSeating);
                        $recognition->profile = $profileOccupation;

                        if ($profileOccupation->seatOccupied) {
                            $seatingOccupied->put($profileOccupation->seatOccupied->number, $profileOccupation->seatOccupied);
                        }

                        $recognition->profileStr = $profileOccupation->seating->pluck('number')->implode(', ');
                    }

                    if ($recognition->overlap) {
                        $count = false;
                        $overlap = true;
                    }
                }
                $personDraws[] = $recognition;
            }
        }

        $occupationPercent = $profileSeating->occupation->count() ? 100 * $seatingOccupied->count() / $profileSeating->occupation->count() : 0;

        $occupation->type = $this->type;
        $occupation->draws = $personDraws;
        $occupation->count = $count;
        $occupation->withOverlap = $overlap;
        $occupation->persons = $seatingOccupied->count();
        $occupation->percent = $occupationPercent;
        $occupation->percentLevel = $this->getOccupationLevel($occupationPercent);
        $occupation->seatingOccupied = $seatingOccupied;
        $occupation->seatingOccupiedStr = $seatingOccupied->count() ? $seatingOccupied->keys()->sort()->implode(', ') : "";

        return $occupation;
    }

    public function getOccupationLevel($op)
    {
        $op = intval($op);
        $level = 1;
        if ($op >= 35) {
            $level = 2;
        }
        if ($op > 50) {
            $level = 3;
        }
        return $level;
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