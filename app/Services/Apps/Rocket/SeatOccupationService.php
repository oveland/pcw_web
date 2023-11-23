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

        // Should be call 2 times to persistenceActivate (Topology 1 criteria - T1) which is complemented by totalPersistenceCounts (Topology 2 - T2 criteria)
        $this->persistenceActivate($currentOccupation->seatingOccupied, $prevOccupation->seatingOccupied, false, $statusDR, $currentOccupation->seatingCounts);
        $this->totalPersistenceCounts($currentOccupation->seatingCounts, $prevOccupation->seatingCounts, $currentOccupation->seatingOccupied, $statusDR);
        $this->persistenceActivate($currentOccupation->seatingOccupied, $prevOccupation->seatingOccupied, false, $statusDR, $currentOccupation->seatingCounts);
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

                //if($routeId == 338)dump('$seatReleaseThreshold = ' . $seatReleaseThreshold, '$seatActivateThreshold = ' . $seatActivateThreshold);

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
     * @param $currentOccupied
     * @param $prevOccupied
     * @param $withOverlap
     * @param StatusDR $statusDR
     * @param $seatingCounts
     * @return void
     */
    private function persistenceActivate(&$currentOccupied, $prevOccupied, $withOverlap = false, StatusDR $statusDR, $seatingCounts)
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

                $configT2Seat = $this->getConfigT2($seat, $statusDR);
                $seatActivateThresholdT2 = $configT2Seat['countFrom'];

                $persistentPrev = $prevOccupied->get($seat);
                $counterActivate = intval($persistentPrev->counterActivate ?? 1);

                $newData = collect($data);
                $counterRelease = $newData->get('counterRelease');

                if ($statusDR->isActive()) {
                    $newData->put('detected', false);

                    if (!$persistentPrev) { // On first activation event in seat
                        $counterActivate = 1;

                        if ($configT2Seat['complementsT1'] && $seatingCounts->get($seat)->counted) { // T2 Complements T1 setting $counterActivate = $seatActivateThreshold
                            $counterActivate = $seatActivateThreshold;
                        }

                        $newData->put('detected', true);
                    } else {
                        if (!$counterRelease || $counterRelease <= 0) {
                            $counterActivate++;
                            $newData->put('detected', true);
                        }

                        if(!$persistentPrev->counted) {
                            if ($configT2Seat['complementsT1'] && $seatingCounts->get($seat)->counted) { // T2 Complements T1 setting $counterActivate = $seatActivateThreshold
                                $counterActivate = $seatActivateThreshold;
                                $newData->put('detected', true);
                            }
                        }
                    }


                    $counted = $counterActivate >= $seatActivateThreshold;

                    $prevCounterActivate = $persistentPrev ? collect($persistentPrev)->get('counterActivate') : 0;
                    $risingEvent = $counterActivate > $prevCounterActivate;

                    $newData->put('counterActivate', $counterActivate);
                    $newData->put('initialCount', $counterActivate == $seatActivateThreshold - 2);
                    $newData->put('beforeCount', $counterActivate == $seatActivateThreshold - 1);
                    $newData->put('activated', $counterActivate == $seatActivateThreshold && $risingEvent);
                    $newData->put('counted', $counted);
                    $newData->put('seatActivateThreshold', $seatActivateThreshold);
                    $newData->put('risingEvent', $risingEvent);

                    $currentOccupied->put($seat, (object)$newData->toArray());
                }
            }
        }
    }

    function getConfigT2($seat, StatusDR $statusDR)
    {
        $routeId = $statusDR->getRouteId();

        $configT2SeatAll = $this->configSeating[$seat]['T2'];
        $configT2Seat = $configT2SeatAll['default'];

        if($routeId && $statusDR->dr->route->isLarge()) $configT2Seat = $configT2SeatAll['defaultLargeRoutes'];

        if ($routeId && isset($configT2SeatAll['routes']) && isset($configT2SeatAll['routes'][$routeId])) $configT2Seat = $configT2SeatAll['routes'][$routeId];

        return $configT2Seat;
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
            $configT2Seat = $this->getConfigT2($seat, $statusDR);
            $seatActivateThresholdT2 = $configT2Seat['countFrom'];

            $seatingOccupiedInfo = $seatingOccupied->get($seat);
            $prevData = $prevSeatingCounts->get($seat);

            $data->photoSeq = $prevData->photoSeq + 1;

            $scores = collect([
                'default' => 1,
                'promoted' => 3,
            ]);
            $promotedConfidence = 70;

            switch ($statusDR->text) {
                case 'start':
                    if ($seatingOccupiedInfo) $data->pa = $seatingOccupiedInfo->confidence > $promotedConfidence ? $scores->get('promoted') : $scores->get('default');
                    break;
                case 'in':
                    if ($seatingOccupiedInfo && $seatingOccupiedInfo->detected) {
                        $firstPersistence = $prevData->pa == 0;
                        $promotedScore = $firstPersistence ? $scores->get('promoted') : $scores->get('default');
                        $data->pa = $prevData->pa + ($seatingOccupiedInfo->confidence > $promotedConfidence ? $promotedScore : $scores->get('default'));
                    } else {
                        $data->pa = $prevData->pa;
                    }
                    break;
                default:
                    $data->pa = 0;
                    $data->photoSeq = 0;
                    break;
            }

            //$seatingActivated = $seatingCounts->put($seat, $data)->filter(function ($data) {
            //    return collect($data)->get('pa') > 0;
            //});
            //$averageCount = $seatingActivated->average('pa');
            //$averageCount = 1;
            //$data->counted = $averageCount > 0 && $data->pa >= $averageCount * 0.3 && ($data->pa / $data->photoSeq) > 0.1;

            $data->counted = $data->pa >= $seatActivateThresholdT2;

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