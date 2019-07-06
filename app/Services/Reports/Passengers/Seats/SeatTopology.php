<?php


namespace App\Services\Reports\Passengers\Seats;


use App\Models\Passengers\Passenger;

abstract class SeatTopology
{
    private $distribution;

    public function __construct($distribution)
    {
        $this->distribution = $distribution;
    }

    function makeHtmlTemplate(Passenger $passenger)
    {
        $hexSeating = $passenger->hexSeats;
        $seatingStatus = self::getSeatingStatusFromHex($hexSeating);
        return view('reports.passengers.sensors.seats.topologies.gualas', compact('seatingStatus', 'hexSeating'));
    }

    function getSeatingStatus(Passenger $passenger)
    {
        return [
            'seatingStatus' => self::getSeatingStatusFromHex($passenger->hexSeats),
            'hexSeating' => $passenger->hexSeats,
            'location' => [
                'latitude' => $passenger->latitude,
                'longitude' => $passenger->longitude
            ],
            'date' => $passenger->date->toDateString(),
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
     * @param $channelStatusHexadecimal
     * @return object
     */
    function getSeatingStatusFromHex($channelStatusHexadecimal)
    {
        $seatingStatusFromHex = collect([]);
        
        if (!$this->distribution || strlen($channelStatusHexadecimal) < 6) return null;

        $seatingStatusBinary = self::decodeChannelStatusFromHex($channelStatusHexadecimal);

        foreach ($this->distribution as $row => $distributionSeating) {
            if( is_array($distributionSeating) )$seatingStatusFromHex->put($row, self::decodeSeatingStatus($distributionSeating, $seatingStatusBinary));
        }

        return $seatingStatusFromHex;
    }

    /**
     * Make distribution for seating rows with current status
     *
     * @param $distributionSeating
     * @param $seatingStatusBinary
     * @return object
     */
    function decodeSeatingStatus($distributionSeating, $seatingStatusBinary)
    {
        $seatingStatus = array();

        foreach ($distributionSeating as $seat => $sensors) {
            $seatingStatus[$seat] = 0;

            if( is_array($sensors) ){
                foreach ($sensors as $sensor) {
                    if ($seatingStatusBinary[$sensor - 1] == 1) {
                        $seatingStatus[$seat] = 1;
                    }
                }
            }else{
                if ($seatingStatusBinary[$sensors - 1] == 1) {
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
    function decodeChannelStatusFromHex($seatingStatusHexadecimal)
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