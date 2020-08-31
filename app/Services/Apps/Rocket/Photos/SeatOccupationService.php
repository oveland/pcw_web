<?php


namespace App\Services\Apps\Rocket\Photos;


use App\Models\Apps\Rocket\ConfigProfile;
use App\Models\Vehicles\Vehicle;
use App\Services\Apps\Rocket\ConfigProfileService;
use Illuminate\Support\Collection;

class SeatOccupationService
{
    protected const THRESHOLD_ACTIVATE = 2;
    protected const THRESHOLD_RELEASE = 2;

    /**
     * @var Vehicle
     */
    private $vehicle;

    /**
     * @var array
     */
    private $configSeating;

    /**
     * ConfigProfileService constructor.
     * @param Vehicle $vehicle
     */
    function __construct(Vehicle $vehicle)
    {
        $this->vehicle = $vehicle;
        $configService = new ConfigProfileService($vehicle);
        $this->configSeating = collect($configService->get()->config)->get('seating');
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
     * @param Collection $currentOccupied
     * @param Collection $prevOccupied
     * @param bool $withOverlap
     */
    private function persistenceRelease(&$currentOccupied, $prevOccupied, $withOverlap = false)
    {
        foreach ($prevOccupied as $seat => $data) {
            $seatReleaseThreshold = $this->configSeating[$seat]['persistence']['release'];

            $newData = collect($data);
            $persistentCurrent = $currentOccupied->get($seat);

            $counterRelease = intval($newData->get('counterRelease') ?? 0);
            if (!$persistentCurrent || $withOverlap) {
                $counterRelease++;

                $newData->put('counterRelease', $counterRelease);

                if ($counterRelease < $seatReleaseThreshold && $newData->get('counterActivate') >= 2) {
                    $newData->put('lockedReleased', $counterRelease);
                    $currentOccupied->put($seat, (object)$newData->toArray());
                } else {
                    $currentOccupied->forget($seat);
                }
            } else {
                $currentOccupied->put($seat, (object)collect($persistentCurrent)->put('counterRelease', 0)->toArray());
            }
        }
    }

    /**
     * @param Collection $currentOccupied
     * @param Collection $prevOccupied
     * @param bool $withOverlap
     */
    private function persistenceActivate(&$currentOccupied, $prevOccupied, $withOverlap = false)
    {
        foreach ($currentOccupied as $seat => $data) {
            $seatActivateThreshold = $this->configSeating[$seat]['persistence']['activate'];

            $newData = collect($data);
            $persistentPrev = $prevOccupied->get($seat);

            $counterActivate = intval($persistentPrev->counterActivate ?? 1);

            $counterRelease = $newData->get('counterRelease');

            if ($persistentPrev && (!$counterRelease || $counterRelease <= 0)) {
                $counterActivate++;
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

    /**
     * @param Collection $currentOccupied
     * @param Collection $prevOccupied
     * @return Collection
     */
    public function getSeatingReleased($currentOccupied, $prevOccupied)
    {
        $seatingRelease = collect([]);
        if ($prevOccupied->count() && $prevOccupied->count() || true) {
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
     * @param Collection $currentOccupied
     * @return Collection
     */
    public function getSeatingActivated($currentOccupied)
    {
        return $currentOccupied->filter(function ($data) {
            return collect($data)->get('activated');
        });
    }
}