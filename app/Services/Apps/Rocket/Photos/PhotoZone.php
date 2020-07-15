<?php

namespace App\Services\Apps\Rocket\Photos;

use App\Models\Apps\Rocket\ProfileSeat;

abstract class PhotoZone
{
    public $left = 0;
    public $top = 0;
    public $width = 0;
    public $height = 0;
    public $center = [];
    public $largeDetection = false;
    public $type = 'faces';

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * PhotoZone constructor.
     * @param array | object $zone
     */
    public function __construct($zone = null)
    {
        $this->buildZone($zone);
    }

    /**
     * @param $zone
     * @return $this
     */
    public function buildZone($zone)
    {
        if ($zone) {
            $zone = (object)$zone;

            $this->left = $zone->left;
            $this->top = $zone->top;
            $this->width = $zone->width;
            $this->height = $zone->height;
            $this->center = (object)$zone->center;

            $this->largeDetection = isset($zone->largeDetection) ? $zone->largeDetection : false;
        }

        return $this;
    }

    public function L()
    {
        return $this->left + $this->width;
    }

    public function T()
    {
        return $this->top + $this->height;
    }

    public function area()
    {
        return $this->width * $this->height;
    }

    public function include(PhotoZone $zone)
    {
        $centerZone = $zone->center;

        $refX = $centerZone->left - $this->left;
        $refY = $centerZone->top - $this->top;

        if ($refX >= 0 && $refY >= 0) {
            if ($this->width >= $refX && $this->height >= $refY) {
                return true;
            }
        }

        return false;
    }

    public function intersectArea(PhotoZone $zone)
    {
        $baseArea = ($this->L() < $zone->L() ? $this->L() : $zone->L()) - ($this->left > $zone->left ? $this->left : $zone->left);
        $heightArea = ($this->T() < $zone->T() ? $this->T() : $zone->T()) - ($this->top > $zone->top ? $this->top : $zone->top);
        return $baseArea * $heightArea;
    }

    public function overlapArea(PhotoZone $zone)
    {
        $percent = (100 / $this->width) * $zone->width;
//        $percent = (100 / $this->area()) * $zone->area();
        return 100 - abs(100 - $percent);
    }

    /**
     * @param PhotoZone $zone
     * @return bool
     */
    public function isInsideOf(PhotoZone $zone)
    {
        return $zone->include($this) && (
                $this->left >= $zone->left &&
                $this->top >= $zone->top &&
                $this->width <= $zone->width &&
                $this->height <= $zone->height
            );
    }

    /**
     * @param ProfileSeat $profileSeating
     * @return object
     */
    function getProfileOccupation(ProfileSeat $profileSeating)
    {
        $seatingWithinZoneDetected = collect([]);
        foreach (collect($profileSeating->occupation)->sortBy('number') as $profileSeat) {
            $candidateSeatingZone = new $this($profileSeat);
            $profileSeat = $this->profileWithArea($profileSeat);

            if ($candidateSeatingZone->include($this)) {
                $seatingWithinZoneDetected->push($profileSeat);
            }
        }

        $seatOccupied = null;
        if ($seatingWithinZoneDetected->count() > 1) {
            $prevSeat = null;
            foreach ($seatingWithinZoneDetected as &$profileSeat) {
                $intersectedArea = $profileSeat->intersectedArea;
                $prevIntersectedArea = $prevSeat ? $prevSeat->intersectedArea : 0;

                $overlapArea = $profileSeat->overlapArea;
                $prevOverlapArea = $prevSeat ? $prevSeat->overlapArea : 0;

                $distanceToCenter = $profileSeat->distanceToCenter;
                $prevDistanceToCenter = $prevSeat ? $prevSeat->distanceToCenter : 0;

                $profileSeat->distanceToCenter = $distanceToCenter;

                $largeDetection = $this->largeDetection;

                switch ($this->type) {
                    case 'faces':
                        $criteria = $intersectedArea > $prevIntersectedArea;
                        break;
                    default:
                        $criteria = $distanceToCenter < $prevDistanceToCenter;

//                        $criteria = $largeDetection && ($this->width > 20) ? $overlapArea > $prevOverlapArea : $distanceToCenter < $prevDistanceToCenter;

                        break;
                }

//                $criteria = $distanceToCenter < $prevDistanceToCenter;


                if ($criteria || $prevSeat === null) {
                    $seatOccupied = $profileSeat;
                }

                $prevSeat = $profileSeat;
            }
        } else if ($seatingWithinZoneDetected->count() == 1) {
            $seatOccupied = $this->profileWithArea($seatingWithinZoneDetected->first());
        }

        return (object)[
            'seating' => $seatingWithinZoneDetected,
            'seatOccupied' => $seatOccupied
        ];
    }

    public function getDistanceToCenter(PhotoZone $zone)
    {
        $a = abs($zone->center->left - $this->center->left);
        $b = abs($zone->center->top - $this->center->top);

        return sqrt(($a * $a) + ($b * $b));
    }

    /**
     * @param $profileSeat
     * @return object
     */
    public function profileWithArea($profileSeat)
    {
        $seatZone = new $this($profileSeat);
        $profileSeat = (object)$profileSeat;
        $profileSeat->area = $seatZone->area();
        $profileSeat->areaDetected = $this->area();
        $profileSeat->intersectedArea = $seatZone->intersectArea($this);
        $profileSeat->overlapArea = $seatZone->overlapArea($this);
        $profileSeat->distanceToCenter = $this->getDistanceToCenter($seatZone);
        $profileSeat->typeZone = $this->type;

        return $profileSeat;
    }
}