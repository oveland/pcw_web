<?php


namespace App\Services\Apps\Rocket;

use App\Models\Apps\Rocket\ProfileSeat;
use Illuminate\Support\Collection;

class SeatOccupationService
{
    /**
     * @var array
     */
    private $configSeating;

    /**
     * ConfigProfileService constructor.
     * @param ProfileSeat $profileSeat
     */
    function __construct(ProfileSeat $profileSeat)
    {
        $configService = new ConfigProfileService($profileSeat);
        $this->configSeating = collect($configService->getConfigProfile()->config)->get('seating');
    }

    /**
     * @param $currentOccupied
     * @param $prevOccupied
     * @param false $withOverlap
     * @param $statusDispatch
     */
    public function processPersistenceSeating(&$currentOccupied, $prevOccupied, $withOverlap = false, $statusDispatch, $routeId = null)
    {
        $this->persistenceRelease($currentOccupied, $prevOccupied, $withOverlap, $statusDispatch, $routeId);
        $this->persistenceActivate($currentOccupied, $prevOccupied, false, $statusDispatch, $routeId);
    }

    /**
     * @param Collection | array $currentOccupied
     * @param Collection | array $prevOccupied
     * @param bool $withOverlap
     * @param $statusDispatch
     */
    private function persistenceRelease(&$currentOccupied, $prevOccupied, $withOverlap = false, $statusDispatch, $routeId = null)
    {
        if (!$withOverlap) {
            foreach ($prevOccupied as $seat => $data) {
                $configSeat = $this->configSeating[$seat];

                $seatReleaseThreshold = $configSeat['persistence']['release'];
                $seatActivateThreshold = $configSeat['persistence']['activate'];

                $a = $seatReleaseThreshold;

                if($routeId && $configSeat['persistenceRoutes'] && isset($configSeat['persistenceRoutes'][$routeId])) {
                    $seatReleaseThreshold = $configSeat['persistenceRoutes'][$routeId]['r'];
                    $seatActivateThreshold = $configSeat['persistenceRoutes'][$routeId]['a'];
                }

                //if($a != $seatReleaseThreshold) dd("Change release: $a by $seatReleaseThreshold on route $routeId");

                $newData = collect($data);

                $persistentInCurrent = $currentOccupied->get($seat);

                $counterRelease = intval($newData->get('counterRelease') ?? 0);
                if (!$persistentInCurrent && ($statusDispatch == 'in' || $statusDispatch == 'start')) {
                    $counterRelease++;

                    $newData->put('counterRelease', $counterRelease);

//                    if ($statusDispatch == 'out' || $statusDispatch == 'none') {
//                        $counterRelease = $seatReleaseThreshold;
//                    }

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
     * @param $statusDispatch
     */
    private function persistenceActivate(&$currentOccupied, $prevOccupied, $withOverlap = false, $statusDispatch, $routeId = null)
    {
        if (!$withOverlap) {
            $currentOccupiedClone = clone $currentOccupied;
            $currentOccupied = collect([]);
            foreach ($currentOccupiedClone as $seat => $data) {
                $configSeat = $this->configSeating[$seat];

                $seatActivateThreshold = $configSeat['persistence']['activate'];
                if($routeId && $configSeat['persistenceRoutes'] && isset($configSeat['persistenceRoutes'][$routeId])) {
                    $seatActivateThreshold = $configSeat['persistenceRoutes'][$routeId]['a'];
                }

                $newData = collect($data);

                $persistentPrev = $prevOccupied->get($seat);

                $counterActivate = intval($persistentPrev->counterActivate ?? 1);

                $counterRelease = $newData->get('counterRelease');

                if ($statusDispatch == 'start' || $statusDispatch == 'in') {
                    if ($persistentPrev && (!$counterRelease || $counterRelease <= 0)) {
                        $counterActivate++;
                    } else if (!$persistentPrev) {
                        $counterActivate = 1;
                    }

                    $prevCounterActivate = $persistentPrev ? collect($persistentPrev)->get('counterActivate') : 0;
                    $risingEvent = $counterActivate > $prevCounterActivate;

                    $newData->put('counterActivate', $counterActivate);
                    $newData->put('initialCount', $counterActivate == $seatActivateThreshold - 2);
                    $newData->put('beforeCount', $counterActivate == $seatActivateThreshold - 1);
                    $newData->put('activated', $counterActivate == $seatActivateThreshold && $risingEvent);
                    $newData->put('counted', $counterActivate >= $seatActivateThreshold);
                    $newData->put('seatActivateThreshold', $seatActivateThreshold);
                    $newData->put('statusDispatch', $statusDispatch);
                    $newData->put('risingEvent', $risingEvent);

                    $currentOccupied->put($seat, (object)$newData->toArray());
                } else {
                    $counterActivate = 0;
                }


            }
        }
    }

    /**
     * @param Collection | array $currentOccupied
     * @param Collection | array $prevOccupied
     * @param false $withOverlap
     * @return Collection
     */
    public function getSeatingReleased($currentOccupied, $prevOccupied, $withOverlap = false)
    {
        $seatingRelease = collect([]);

        if (!$withOverlap) {
            if ($prevOccupied->count() && $prevOccupied->count() || true) {
                foreach ($prevOccupied as $seat => $data) {
                    $inCurrentSeat = $currentOccupied->get($seat);

                    if (!$inCurrentSeat) {
                        $seatingRelease->put($seat, $data);
                    }
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
    public function getSeatingActivated($currentOccupied, $withOverlap = false)
    {
        $seatingActivated = collect([]);

        if (!$withOverlap) {
            $seatingActivated = $currentOccupied->filter(function ($data) {
                return collect($data)->get('activated');
            });
        }

        return $seatingActivated;
    }
}