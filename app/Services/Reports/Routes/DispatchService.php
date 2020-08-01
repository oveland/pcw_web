<?php

namespace App\Services\Reports\Routes;

use App\Models\Company\Company;
use App\Models\Routes\DispatchRegister;
use Illuminate\Support\Collection;

class DispatchService
{
    /**
     * @var Company
     */
    public $company;

    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    /**
     * @param $initialDate
     * @param null $finalDate
     * @param null $route
     * @param null $vehicle
     * @param string $type
     * @return DispatchRegister[] | Collection
     */
    function getTurns($initialDate, $finalDate = null, $route = null, $vehicle = null, $type = 'completed')
    {
        $dr = DispatchRegister::whereCompanyAndDateRangeAndRouteIdAndVehicleId($this->company, $initialDate, $finalDate, $route, $vehicle)->type($type)->get();

        return $dr->map(function (DispatchRegister $dr){
            return $dr->getAPIFields();
        })->sortBy('id')->values();
    }
}