<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 15/04/2018
 * Time: 11:34 PM
 */

namespace App\Services;


use App\Passenger;

class PCWSeatSensorGualas
{
    static function getDistribution()
    {
        return (object)[
            'row1' => [
                5 => [10, 9],
                4 => [8, 7],
                3 => [6, 5],
                2 => [4, 3],
                1 => [2]
            ],
            'row2' => [
                10 => [21, 20],
                9 => [19, 18],
                8 => [17, 16],
                7 => [15, 14],
                6 => [13],
            ]
        ];
    }

    static function makeHtmlTemplate(Passenger $passenger)
    {
        $seatingStatus = self::getSeatingStatusFromHex($passenger->hexSeats);
        return view('reports.passengers.sensors.seats.topologies.gualas', compact('seatingStatus'));
    }

    static function getSeatingStatus(Passenger $passenger)
    {
        return [
            'seatingStatus' => self::getSeatingStatusFromHex($passenger->hexSeats),
            'location' => [
                'latitude' => $passenger->latitude,
                'longitude' => $passenger->longitude
            ],
            'passengers' => $passenger->total,
            'time' => $passenger->date->toTimeString(),
        ];
    }

    /**
     * @param $seatingStatusHexadecimal
     * @return object
     */
    static function getSeatingStatusFromHex($seatingStatusHexadecimal)
    {
        $seatingStatusFromHex = collect([]);
        $distribution = self::getDistribution();
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
                if ($seatingStatusBinary[$sensor] == 1) {
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