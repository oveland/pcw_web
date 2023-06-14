<?php


namespace App\Services\Apps\Rocket\Photos\Rekognition;

use App\Models\Apps\Rocket\ConfigProfile;
use App\Models\Apps\Rocket\ProfileSeat;
use App\Models\Apps\Rocket\Traits\PhotoInterface;
use App\Services\Apps\Rocket\ConfigProfileService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;

abstract class PhotoRekognitionService
{
    protected $type = 'persons';

    /**
     * @var PhotoZone
     */
    public $photoZone;

    /**
     * @var object
     */
    public $config;

    /**
     * @var ProfileSeat
     */
    private $profileSeat;


    /**
     * PhotoRekognitionService constructor.
     * @param PhotoZone $photoZone
     * @param ProfileSeat $profileSeat
     * @throws Exception
     */
    function __construct(PhotoZone $photoZone, ProfileSeat $profileSeat, ConfigProfile $configProfile)
    {
        $this->photoZone = $photoZone;

        $configService = new ConfigProfileService($profileSeat, $configProfile);
        $this->config = $configService->type($this->type);
        $this->profileSeat = $profileSeat;
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
                'widthSeatingAverage' => $rule->widthSeatingAverage,
                'invalidSize' => $rule->invalidSize,
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
        $boxZone = $this->processBoxZone($boundingBox, $confidence);

        $confidence = intval($confidence);

        $rules = collect($this->config->photo->rekognition->rules);

        $rule = collect($rules->filter(function ($rule) use ($confidence) {
            $rule = (object)$rule;
            $rangeArray = collect($rule->range);
            return collect(range($rangeArray->first(), $rangeArray->last()))->contains($confidence);
        })->first());

        $count = $rule->get('count') && !$boxZone->overlap && !$boxZone->largeDetection && !$boxZone->invalidSize;

        if (!$count) {
            $rule->put('color', 'rgba(255, 50, 55, 0.78)');
        }

        if ($boxZone->overlap) {
            $rule->put('color', 'rgba(3, 168, 255, 0.78)');
        }

        if ($boxZone->invalidSize) {
            $rule->put('color', 'rgba(137, 138, 135, 0.1)');
        }

        $background = ($count ? $rule->get('background') : 'rgba(137, 138, 135, 0.1)');

        $rule->put('count', $count && !$boxZone->overlap);
        $rule->put('overlap', $boxZone->overlap);
        $rule->put('background', $background);
        $rule->put('largeDetection', $boxZone->largeDetection);
        $rule->put('relationSize', $boxZone->relationSize);
        $rule->put('invalidSize', $boxZone->invalidSize);
        $rule->put('widthSeatingAverage', $boxZone->widthSeatingAverage);

        return (object)$rule->toArray();
    }

    /**
     * @param $boundingBox > Values are in percent. Relative to photo image size processed with AWS Rekognition
     * @return object
     */
    protected function processBoxZone($boundingBox, $confidence)
    {
        $boundingBox = (object)$boundingBox;

        $configBox = $this->config->photo->rekognition->box;
        $cameraConfig = (object)$this->config->camera;

        $width = $boundingBox->width;
        $height = $boundingBox->height;

        $heightOrig = $boundingBox->heightOrig ?? $height;

        if (isset($boundingBox->center)) {
            $boundingBox->center = (object)$boundingBox->center;
            $centerOrig = (object)($boundingBox->center ?? $boundingBox->centerOrig);
        } else {
            $centerOrig = (object)[
                'left' => $boundingBox->left + $width / 2,
                'top' => $boundingBox->top + $heightOrig / 2,
            ];
        }

        $relationSize = $heightOrig / $width;
        $largeDetection = $cameraConfig->largeDetection && $relationSize >= $configBox->ld;

        $type = $this->type;
        $criteriaWidthSize = $cameraConfig->processWidthSize->$type;
        $invalidSize = $width > $criteriaWidthSize->maxWidth || $width < $criteriaWidthSize->minWidth;

//        if ($confidence == 34.51) dump("Confidence $confidence and $type and invalid Size =" . $invalidSize . " .. $width > $criteriaWidthSize->maxWidth || $width < $criteriaWidthSize->minWidth");

        $overlap = false;
//        if ($this->type == 'persons') {
            //        $overlap = false;
//
//        if (isset($boundingBox->center)) {
//            if ($boundingBox->center->top < 60 || true) {   // For overlaps located on medium screen
//                if ($boundingBox->center->left > 30 && $boundingBox->center->left < 60) { // Only overlaps located on center/front bottom screen
//                    $overlap = ($heightOrig > 30 && $width > 30);
//                }
//            }
//
//            if ($boundingBox->center->top < 45) {   // For overlaps located on medium screen
//                $overlap = $overlap || ($heightOrig > 30 && $width > 13);
//            }
//
//            if ($boundingBox->center->top < 30) { // For overlaps located on top/back screen
//                $overlap = $overlap || ($heightOrig > 30 && $width > 10);
//            }
//        }
//        } else if ($this->type == 'faces') {
//            if (isset($boundingBox->center) && $boundingBox->center->top < 20) {
//                $overlap = ($height > 11 && $width > 6);
//            } else {
//                $overlap = ($height > 20 && $width > 11);
//            }
//
//        }

        $widthSeatingAverage = $cameraConfig->widthSeatingAverage;

        return (object)compact(['overlap', 'relationSize', 'largeDetection', 'invalidSize', 'widthSeatingAverage']);
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
        $occupation = (object)$occupation;

        $draws = collect([]);
        $seatingOccupied = collect([]);
        $seatingCounts = collect([]);

        $count = true;
        $overlap = false;

        if (isset($occupation->draws)) {
            foreach ($occupation->draws as $recognition) {
                $recognition = (object)$recognition;
                if (isset($recognition->count)) {
                    if ($recognition->count) {
                        $this->photoZone->buildZone($recognition->box);
                        $this->photoZone->setType($recognition->type);
                        $profileOccupation = $this->photoZone->getProfileOccupation($this->profileSeat);
                        $recognition->profile = $profileOccupation;

                        if ($profileOccupation->seatOccupied) {
                            $confidence = $recognition->confidence;
                            $seatingOccupiedPrev = $seatingOccupied->get($profileOccupation->seatOccupied->number);
                            if ($seatingOccupiedPrev && $seatingOccupiedPrev->confidence > $confidence) $confidence = $seatingOccupiedPrev->confidence;
                            $profileOccupation->seatOccupied->confidence = $confidence;

                            $seatingOccupied->put($profileOccupation->seatOccupied->number, $profileOccupation->seatOccupied);
                        }

                        $recognition->profileStr = $profileOccupation->seating->pluck('number')->implode(', ');
                        $draws[] = $recognition;
                    }

                    if ($recognition->overlap) {
                        $count = false;
                        $overlap = true;
                    }
                }
            }
        }

        $seatingProfileOccupation = collect($this->profileSeat->occupation);
        $occupationPercent = $seatingProfileOccupation->count() ? 100 * $seatingOccupied->count() / $seatingProfileOccupation->count() : 0;

        $seatingProfileOccupation->each(function ($seat) use ($seatingCounts) {
            $seat = (object)$seat;
            $seatingCounts->put($seat->number, (object)[
                'id' => $seat->id,
                'number' => $seat->number,
                'pa' => 0, // persistence activations
                'pr' => 0, // persistence releases
                'counted' => false,
                'photoSeq' => 0,
            ]);
        });

        $occupation->type = $this->type;
        $occupation->draws = $draws;
        $occupation->count = $count;
        $occupation->withOverlap = $overlap;
        $occupation->persons = $seatingOccupied->count();
        $occupation->percent = $occupationPercent;
        $occupation->percentLevel = $this->getOccupationLevel($occupationPercent);
        $occupation->seatingOccupied = $seatingOccupied;
        $occupation->seatingOccupiedStr = $seatingOccupied->count() ? $seatingOccupied->keys()->sort()->implode(', ') : "";
        $occupation->seatingCounts = $seatingCounts;

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