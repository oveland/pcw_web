<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 15/04/2018
 * Time: 11:34 PM
 */

namespace App\Services\Reports\Passengers;

use App\Models\Vehicles\VehicleSeatDistribution;
use App\Models\Vehicles\VehicleSeatTopology;
use App\Services\Reports\Passengers\Seats\DefaultSeatTopology;
use App\Services\Reports\Passengers\Seats\GualasSeatTopology;
use App\Services\Reports\Passengers\Seats\IntermunicipalSeatTopology;
use App\Services\Reports\Passengers\Seats\SeatTopology;

class SeatDistributionService
{
    /**
     * @var VehicleSeatDistribution
     */
    private $vehicleSeatDistribution;

    public function __construct(VehicleSeatDistribution $vehicleSeatDistribution = null)
    {

        $this->vehicleSeatDistribution = $vehicleSeatDistribution;
    }

    /**
     * @return SeatTopology
     */
    public function getTopology()
    {
        $instance = new DefaultSeatTopology();
        if ($this->vehicleSeatDistribution) {
            $distribution = json_decode($this->vehicleSeatDistribution->json_distribution, true);

            $topology = $this->vehicleSeatDistribution->topology;

            switch ($topology ? $topology->id : 0) {
                case VehicleSeatTopology::GUALAS:
                    $instance = new GualasSeatTopology($distribution);
                    break;
                case VehicleSeatTopology::INTERMUNICIPAL:
                    $instance = new IntermunicipalSeatTopology($distribution);
                    break;
                default:
                    $instance = new DefaultSeatTopology($distribution);
                    break;
            }
        }

        return $instance;
    }
}