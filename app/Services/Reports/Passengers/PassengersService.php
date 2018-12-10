<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 8/12/2018
 * Time: 11:05 PM
 */

namespace App\Services\Reports\Passengers;

class PassengersService
{
    /**
     * @var ConsolidatedService
     */
    public $consolidated;
    /**
     * @var DetailedService
     */
    public $detailed;

    /**
     * PassengersService constructor.
     *
     * @param ConsolidatedService $consolidatedService
     * @param DetailedService $detailedService
     */
    public function __construct(ConsolidatedService $consolidatedService, DetailedService $detailedService)
    {
        $this->consolidated = $consolidatedService;
        $this->detailed = $detailedService;
    }
}