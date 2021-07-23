<?php


namespace App\Services\Apps\Rocket\Photos;

use App\Models\Apps\Rocket\ProfileSeat;
use App\Services\Apps\Rocket\ConfigProfileService;
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
     */
    public function processPersistenceSeating(&$currentOccupied, $prevOccupied, $withOverlap = false)
    {
        $this->persistenceRelease($currentOccupied, $prevOccupied, $withOverlap);
        $this->persistenceActivate($currentOccupied, $prevOccupied);
    }

    /**
     * @param Collection | array $currentOccupied
     * @param Collection | array $prevOccupied
     * @param bool $withOverlap
     */
    private function persistenceRelease(&$currentOccupied, $prevOccupied, $withOverlap = false)
    {
        if (!$withOverlap) {
            foreach ($prevOccupied as $seat => $data) {
                $seatReleaseThreshold = $this->configSeating[$seat]['persistence']['release'];
                $seatActivateThreshold = $this->configSeating[$seat]['persistence']['activate'];

                $newData = collect($data);

                $persistentInCurrent = $currentOccupied->get($seat);

                $counterRelease = intval($newData->get('counterRelease') ?? 0);
                if (!$persistentInCurrent) {
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
     */
    private function persistenceActivate(&$currentOccupied, $prevOccupied, $withOverlap = false)
    {
        if (!$withOverlap) {
            $currentOccupiedClone = clone $currentOccupied;
            foreach ($currentOccupiedClone as $seat => $data) {
                $seatActivateThreshold = $this->configSeating[$seat]['persistence']['activate'];

                $newData = collect($data);

                $persistentPrev = $prevOccupied->get($seat);

                $counterActivate = intval($persistentPrev->counterActivate ?? 1);

                $counterRelease = $newData->get('counterRelease');

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
                $newData->put('risingEvent', $risingEvent);

                $currentOccupied->put($seat, (object)$newData->toArray());
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