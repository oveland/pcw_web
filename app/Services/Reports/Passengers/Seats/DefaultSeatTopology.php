<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 15/04/2018
 * Time: 11:34 PM
 */

namespace App\Services\Reports\Passengers\Seats;

use App\Models\Passengers\Passenger;

class DefaultSeatTopology extends SeatTopology
{
    public function __construct($distribution = null)
    {
        $distribution = $distribution ? $distribution : (object)[
            'row1' => [
                1 => [21],
                2 => [22],
                3 => [23],
                4 => [24],

                5 => [17],
                6 => [18],
                7 => [19],
                8 => [20],

                9 => [13],
                10 => [14],
                11 => [15],
                12 => [16],

                13 => [9],
                14 => [10],
                15 => [11],
                16 => [12],

                17 => [5],
                18 => [6],
                19 => [7],
            ]
        ];;
        parent::__construct($distribution);
    }

    public function makeHtmlTemplate(Passenger $passenger)
    {
        $hexSeating = $passenger->hexSeats;
        $seatingStatus = self::getSeatingStatusFromHex($hexSeating);
        return view('reports.passengers.sensors.seats.topologies.default', compact('seatingStatus', 'hexSeating'));
    }
}