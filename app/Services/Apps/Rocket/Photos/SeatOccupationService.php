<?php


namespace App\Services\Apps\Rocket\Photos;


use Illuminate\Support\Collection;

class SeatOccupationService
{
    protected const THRESHOLD_ACTIVATE = 3;
    protected const THRESHOLD_RELEASE = 3;

    /**
     * @param Collection $currentOccupied
     * @param Collection $prevOccupied
     * @param $withOverlap
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
            $newData = collect($data);
            $persistentCurrent = $currentOccupied->get($seat);

            $counterRelease = intval($newData->get('counterRelease') ?? 0);
            if (!$persistentCurrent || $withOverlap) {
                $counterRelease++;

                $newData->put('counterRelease', $counterRelease);

                if ($counterRelease < self::THRESHOLD_RELEASE && $newData->get('counterActivate') >= 2) {
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
            $newData->put('initialCount', $counterActivate == self::THRESHOLD_ACTIVATE - 2);
            $newData->put('beforeCount', $counterActivate == self::THRESHOLD_ACTIVATE - 1);
            $newData->put('activated', $counterActivate == self::THRESHOLD_ACTIVATE && $risingEvent);
            $newData->put('counted', $counterActivate >= self::THRESHOLD_ACTIVATE);
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