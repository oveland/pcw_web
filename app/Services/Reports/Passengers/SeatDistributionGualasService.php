<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 15/04/2018
 * Time: 11:34 PM
 */

namespace App\Services\Reports\Passengers;


use App\Passenger;
use App\Vehicle;
use App\VehicleSeatDistribution;

class SeatDistributionGualasService
{
    /**
     * @param Vehicle $vehicle
     * @return mixed|null
     */
    static function getDistribution(Vehicle $vehicle)
    {
        $vehicleSeatDistribution = VehicleSeatDistribution::where('vehicle_id', $vehicle->id)->get()->first();

        if ($vehicleSeatDistribution) {
            return json_decode($vehicleSeatDistribution->json_distribution, true);
        }
        return null;
    }

    static function makeHtmlTemplate(Passenger $passenger)
    {
        $hexSeating = $passenger->hexSeats;
        $seatingStatus = self::getSeatingStatusFromHex($hexSeating, $passenger->vehicle);
        return view('reports.passengers.sensors.seats.topologies.gualas', compact('seatingStatus', 'hexSeating'));
    }

    static function getSeatingStatus(Passenger $passenger)
    {
        return [
            'seatingStatus' => self::getSeatingStatusFromHex($passenger->hexSeats, $passenger->vehicle),
            'hexSeating' => $passenger->hexSeats,
            'location' => [
                'latitude' => $passenger->latitude,
                'longitude' => $passenger->longitude
            ],
            'time' => $passenger->date->toTimeString(),
            'route' => $passenger->dispatchRegister->route ?? __('No Route'),
            'passengers' => $passenger->total,
            'passengersPlatform' => $passenger->total_platform,
            'vehicleStatus' => $passenger->vehicleStatus->des_status,
            'vehicleStatusIconClass' => $passenger->vehicleStatus->icon_class,
            'vehicleStatusMainClass' => $passenger->vehicleStatus->main_class,
        ];
    }

    /**
     * @param $seatingStatusHexadecimal
     * @param $vehicle
     * @return object
     */
    static function getSeatingStatusFromHex($seatingStatusHexadecimal, Vehicle $vehicle)
    {
        $seatingStatusFromHex = collect([]);
        $distribution = self::getDistribution($vehicle);

        if (!$distribution || strlen($seatingStatusHexadecimal) < 6) return null;

        $seatingStatusBinary = self::decodeSeatingStatusFromHex($seatingStatusHexadecimal);

        foreach ($distribution as $row => $distributionSeating) {
            $seatingStatusFromHex->put($row, self::makeDistribution($distributionSeating, $seatingStatusBinary));
        }

        return $seatingStatusFromHex;
    }

    /**
     * Make distribution for seating rows with current status
     *
     * @param $distribution
     * @param $seatingStatusBinary
     * @return object
     */
    static function makeDistribution($distribution, $seatingStatusBinary)
    {
        $seatingStatus = array();
        foreach ($distribution as $seat => $sensors) {
            $seatingStatus[$seat] = 0;
            foreach ($sensors as $sensor) {
                if ($seatingStatusBinary[$sensor - 1] == 1) {
                    $seatingStatus[$seat] = 1;
                }
            }
        }
        return (object)$seatingStatus;
    }

    /**
     * Decode string hex to binary seating status
     *
     * @param $seatingStatusHexadecimal
     * @return array
     */
    static function decodeSeatingStatusFromHex($seatingStatusHexadecimal)
    {
        $seatingStatusBinary = array();
        if ($seatingStatusHexadecimal && $seatingStatusHexadecimal != "") {
            $binaryStatus = str_pad(base_convert($seatingStatusHexadecimal, 16, 2), 24, "0", STR_PAD_LEFT);
            $l = strlen($binaryStatus) - 1;
            for ($i = 0; $i < strlen($binaryStatus); $i++) {
                $seatingStatusBinary[$i] = substr($binaryStatus, ($l - $i), 1);
            }
        }
        return $seatingStatusBinary;
    }
}