<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 15/04/2018
 * Time: 11:34 PM
 */

namespace App\Services\Reports\Passengers\Seats;

use App\Models\Passengers\Passenger;

class GualasSeatTopology extends SeatTopology
{
    public function __construct($distribution)
    {
        parent::__construct($distribution);
    }

    function makeHtmlTemplate(Passenger $passenger)
    {
        $hexSeating = $passenger->hexSeats;
        $hexSeatFromFrameCounter = $this->getHexSeatFromFrameCounter($passenger->frame);
        $seatingStatus = self::getSeatingStatusFromHex($hexSeating);
        $seatingStatusRT = self::getSeatingStatusFromHex($hexSeatFromFrameCounter); // Seating status on real time (Only for counter of vehicle 15 cootransol)
        //dd($seatingStatusRT);
        return view('reports.passengers.sensors.seats.topologies.gualas', compact(['seatingStatus', 'seatingStatusRT', 'hexSeating']));
    }
}