<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 7/11/2018
 * Time: 10:46 PM
 */

namespace App\Interfaces;


use App\Models\Passengers\Passenger;
use App\Models\Vehicles\Vehicle;

interface SeatDistributionInterface
{
    public static function getDistribution(Vehicle $vehicle);

    static function makeHtmlTemplate(Passenger $passenger);
}