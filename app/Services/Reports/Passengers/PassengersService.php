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
     * PassengersService constructor.
     *
     * @param ConsolidatedService $consolidatedService
     */
    public function __construct(ConsolidatedService $consolidatedService)
    {
        $this->consolidated = $consolidatedService;
    }
}