<?php


namespace App\Services\Apps\Rocket;

use App\Models\Apps\Rocket\ConfigProfile;
use App\Services\Apps\Rocket\Photos\StatusDR;
use Illuminate\Support\Collection;

class SeatOccupationService
{
    /**
     * @var array
     */
    private $configSeating;

    /**
     * @param ConfigProfile $configProfile
     */
    function __construct(ConfigProfile $configProfile)
    {
        $this->configSeating = collect($configProfile->config)->get('seating');
    }

    /**
     * @param $currentOccupation
     * @param $prevOccupation
     * @param StatusDR $statusDR
     */
    function processPersistenceSeating($currentOccupation, $prevOccupation, StatusDR $statusDR)
    {
        $this->persistenceRelease($currentOccupation->seatingOccupied, $prevOccupation->seatingOccupied, $currentOccupation->withOverlap, $statusDR);
        $this->persistenceActivate($currentOccupation->seatingOccupied, $prevOccupation->seatingOccupied, false, $statusDR);
        $this->totalPersistenceCounts($currentOccupation->seatingCounts, $prevOccupation->seatingCounts, $currentOccupation->seatingOccupied, $statusDR);
    }

    /**
     * @param Collection | array $currentOccupied
     * @param Collection | array $prevOccupied
     * @param bool $withOverlap
     * @param StatusDR $statusDR
     */
    private function persistenceRelease(&$currentOccupied, $prevOccupied, $withOverlap = false, StatusDR $statusDR)
    {
        if ($statusDR->start) $prevOccupied = collect([]);

        if (!$withOverlap) {
            $routeId = $statusDR->getRouteId();

            foreach ($prevOccupied as $seat => $data) {
                $configSeat = $this->configSeating[$seat];

                $seatReleaseThreshold = $configSeat['persistence']['release'];
                $seatActivateThreshold = $configSeat['persistence']['activate'];

                if ($routeId && $configSeat['persistenceRoutes'] && isset($configSeat['persistenceRoutes'][$routeId])) {
                    $seatReleaseThreshold = $configSeat['persistenceRoutes'][$routeId]['r'];
                    $seatActivateThreshold = $configSeat['persistenceRoutes'][$routeId]['a'];
                }

                $newData = collect($data);

                $persistentInCurrent = $currentOccupied->get($seat);

                $counterRelease = intval($newData->get('counterRelease') ?? 0);
                if (!$persistentInCurrent && $statusDR->isActive()) {
                    $counterRelease++;

                    $newData->put('counterRelease', $counterRelease);

                    if ($counterRelease < $seatReleaseThreshold && $newData->get('counterActivate') >= $seatActivateThreshold) {
                        $currentOccupied->put($seat, (object)$newData->toArray());
                    } else {
                        $currentOccupied->forget($seat);
                    }
                } else {
                    $currentOccupied->put($seat, (object)collect($persistentInCurrent)->put('counterRelease', 0)->toArray());
                }
            }
        } else {
            $currentOccupied = $prevOccupied;
        }
    }

    /**
     * @param Collection | array $currentOccupied
     * @param Collection | array $prevOccupied
     * @param bool $withOverlap
     * @param StatusDR $statusDR
     */
    private function persistenceActivate(&$currentOccupied, $prevOccupied, $withOverlap = false, StatusDR $statusDR)
    {
        if ($statusDR->start) $prevOccupied = collect([]);

        if (!$withOverlap) {
            $routeId = $statusDR->getRouteId();

            $currentOccupiedClone = clone $currentOccupied;
            $currentOccupied = collect([]);
            foreach ($currentOccupiedClone as $seat => $data) {
                $configSeat = $this->configSeating[$seat];

                $seatActivateThreshold = $configSeat['persistence']['activate'];
                if ($routeId && $configSeat['persistenceRoutes'] && isset($configSeat['persistenceRoutes'][$routeId])) {
                    $seatActivateThreshold = $configSeat['persistenceRoutes'][$routeId]['a'];
                }

                $newData = collect($data);

                $persistentPrev = $prevOccupied->get($seat);
                $counterActivate = intval($persistentPrev->counterActivate ?? 1);

                $counterRelease = $newData->get('counterRelease');

                if ($statusDR->isActive()) {
                    $newData->put('detected', false);
                    if ($persistentPrev && (!$counterRelease || $counterRelease <= 0)) {
                        $counterActivate++;
                        $newData->put('detected', true);
                    } else if (!$persistentPrev) {
                        $counterActivate = 1;
                        $newData->put('detected', true);
                    }

                    $prevCounterActivate = $persistentPrev ? collect($persistentPrev)->get('counterActivate') : 0;
                    $risingEvent = $counterActivate > $prevCounterActivate;

                    $newData->put('counterActivate', $counterActivate);
                    $newData->put('initialCount', $counterActivate == $seatActivateThreshold - 2);
                    $newData->put('beforeCount', $counterActivate == $seatActivateThreshold - 1);
                    $newData->put('activated', $counterActivate == $seatActivateThreshold && $risingEvent);
                    $newData->put('counted', $counterActivate >= $seatActivateThreshold);
                    $newData->put('seatActivateThreshold', $seatActivateThreshold);
                    $newData->put('risingEvent', $risingEvent);

                    $currentOccupied->put($seat, (object)$newData->toArray());
                }
            }
        }
    }


    /**
     * @param $seatingCounts
     * @param $prevSeatingCounts
     * @param $seatingOccupied
     * @param StatusDR $statusDR
     */
    private function totalPersistenceCounts(&$seatingCounts, $prevSeatingCounts, $seatingOccupied, StatusDR $statusDR)
    {
        $seatingCountsClone = clone $seatingCounts;
        $seatingCounts = collect([]);

        foreach ($seatingCountsClone as $seat => $data) {
            $seatingOccupiedInfo = $seatingOccupied->get($seat);
            $prevData = $prevSeatingCounts->get($seat);

            $data->photoSeq = $prevData->photoSeq + 1;

            switch ($statusDR->text) {
                case 'start':
                    if ($seatingOccupiedInfo) $data->pa = $seatingOccupiedInfo->confidence > 70 ? 3 : 0.5;
                    break;
                case 'in':
                    if ($seatingOccupiedInfo && $seatingOccupiedInfo->detected) {
                        $step = $prevData->pa == 0 ? 3 : 1;
                        $data->pa = $prevData->pa + ($seatingOccupiedInfo->confidence > 70 ? $step : 0.5);
                    } else {
                        $data->pa = $prevData->pa;
                    }
                    break;
                default:
                    $data->pa = 0;
                    $data->photoSeq = 0;
                    break;
            }

            $seatingActivated = $seatingCounts->put($seat, $data)->filter(function ($data) {
                return collect($data)->get('pa') > 0;
            });

            $averageCount = $seatingActivated->average('pa');

//            $data->counted = $averageCount > 0 && $data->pa >= $averageCount * 0.3; // && $seatingActivated->count() > 1;
            $data->counted = $averageCount > 0 && $data->pa >= $averageCount * 0.3;// && ($data->pa / $data->photoSeq) > 0.3;
//            $data->counted = $data->pa >= 2;

            $seatingCounts->put($seat, $data);
        }
    }

    /**
     * @param Collection | array $currentOccupied
     * @param Collection | array $prevOccupied
     * @param false $withOverlap
     * @return Collection
     */
    function getSeatingReleased($currentOccupied, $prevOccupied, $withOverlap = false)
    {
        $seatingRelease = collect([]);

        if (!$withOverlap) {
            foreach ($prevOccupied as $seat => $data) {
                $inCurrentSeat = $currentOccupied->get($seat);

                if (!$inCurrentSeat) {
                    $seatingRelease->put($seat, $data);
                }
            }
        }

        return $seatingRelease;
    }


    /**
     * @param Collection | array $currentOccupied
     * @param false $withOverlap
     * @return mixed
     */
    function getSeatingActivated($currentOccupied, $withOverlap = false)
    {
        $seatingActivated = collect([]);

        if (!$withOverlap) {
            $seatingActivated = $currentOccupied->filter(function ($data) {
                return collect($data)->get('activated');
            });
        }

        return $seatingActivated;
    }

    /**
     * @param $seatingCounts
     * @param false $withOverlap
     * @return mixed
     */
    function getSeatingCounted($seatingCounts, $withOverlap = false)
    {
        $seatingCounts = collect($seatingCounts);
        $seatingCounted = collect([]);

        if (!$withOverlap) {
            $seatingCounted = $seatingCounts->filter(function ($data) {
                return collect($data)->get('counted');
            });
        }

        return $seatingCounted;
    }
}