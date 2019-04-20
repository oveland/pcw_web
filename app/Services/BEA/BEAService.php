<?php


namespace App\Services\BEA;


use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;

class BEAService
{
    public function __construct()
    {

    }

    /**
     * @param $vehicleReport
     * @return Collection
     * @throws Exception
     */
    function getBEAMarksFrom($vehicleReport)
    {
        $allBEAMarks = $this->getAllBEAMarks();

        return $allBEAMarks->filter(function ($beaMark) use ($vehicleReport) {
            return $beaMark->turn->vehicle->id == $vehicleReport;
        });
    }

    /**
     * @return Collection
     * @throws Exception
     */
    function getAllBEAMarks()
    {
        $beaMarks = collect([]);

        $vehicles = $this->getAllVehicles();
        $drivers = $this->getAllDrivers();
        $routes = $this->getAllRoutes();
        $travelRoutes = $this->getAllTravelRoutes();

        foreach ($vehicles as $vehicle) {
            $date = $initialDate = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d')." 06:00:00");
            foreach (range(1, 8) as $index) {
                $beaRoute = $routes->where('id', random_int(1, 9))->first();
                $beaDriver = $drivers->where('id', random_int(1, 9))->first();
                $beaRouteTravel = $travelRoutes->where('id', random_int(1, 2))->first();

                $beaTurn = (object)[
                    'driver' => $beaDriver,
                    'route' => $beaRoute,
                    'vehicle' => $vehicle,
                ];

                $initialDate = $date->copy();
                $finalDate = $date->addMinutes(random_int(60,120))->copy();
                $duration = $finalDate->diff($initialDate);

                $passengersUp = random_int(40, 50);
                $passengersDown = random_int(40, 50);

                $imBeaMax = $passengersUp + random_int(1, 10);
                $imBeaMin = $passengersDown - random_int(1, 10);

                $totalBEA = (($imBeaMax + $imBeaMin) / 2) * 2500;

                $beaMarks->push((object)[
                    'id' => $index,
                    'turn' => $beaTurn,
                    'initialDate' => $initialDate,
                    'finalDate' => $finalDate,
                    'duration' => $duration->h."h ".$duration->i." m",
                    'trajectory' => $beaRouteTravel,
                    'passengersUp' => $passengersUp,
                    'passengersDown' => $passengersDown,
                    'imBeaMax' => $imBeaMax,
                    'imBeaMin' => $imBeaMin,
                    'totalBEA' => $totalBEA,
                    'passengersBEA' => $passengersUp > $passengersDown ? $passengersUp : $passengersDown,
                ]);
            }
        }

        return $beaMarks;
    }

    /**
     * @return Collection
     */
    function getAllVehicles()
    {
        $vehicles = collect([]);
        foreach (range(1, 9) as $vehicleId) {
            $vehicles->push((object)[
                'id' => $vehicleId,
                'number' => "10$vehicleId",
                'plate' => "BEA-10$vehicleId",
            ]);
        }

        return $vehicles;
    }

    /**
     * @return Collection
     */
    function getAllDrivers()
    {
        $drivers = collect([]);
        foreach (range(1, 9) as $driverId) {
            $drivers->push((object)[
                'id' => $driverId,
                'name' => __('Driver') . " $driverId",
            ]);
        }

        return $drivers;
    }

    /**
     * @return Collection
     */
    function getAllRoutes()
    {
        $routes = collect([]);
        foreach (range(1, 9) as $routeId) {
            $routes->push((object)[
                'id' => $routeId,
                'name' => __('Route') . " $routeId",
            ]);
        }

        return $routes;
    }

    /**
     * @return Collection
     */
    function getAllTravelRoutes()
    {
        $travelRoutes = collect([]);
        $travelRoutes->push((object)['id' => 1, 'name' => 'IDA']);
        $travelRoutes->push((object)['id' => 2, 'name' => 'REGRESO']);
        return $travelRoutes;
    }
}