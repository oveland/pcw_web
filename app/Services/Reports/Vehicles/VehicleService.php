<?php


namespace App\Services\Reports\Vehicles;


use App\Models\Company\Company;
use App\Models\Drivers\Driver;
use App\Models\Proprietaries\Proprietary;
use App\Services\Exports\Vehicles\VehicleExportService;
use Illuminate\Support\Collection;

class VehicleService
{
    /**
     * @var VehicleExportService
     */
    public $export;

    public function __construct(VehicleExportService $vehicleExportService)
    {
        $this->export = $vehicleExportService;
    }

    /**
     * @param Company $company
     * @return Collection
     */
    public function makeVehicleReport(Company $company)
    {
        $vehicleReport = collect([]);

        $vehicles = $company->vehicles()
            ->with(['proprietary', 'driver'])
            ->orderBy('active')
            ->get();

        $vehicles = $vehicles->sortBy(function($v){
            return ($v->active ? '0':'999') . intval($v->number);
        });

        foreach ($vehicles as $vehicle) {
            $vehicleReport->push((object)[
                'vehicle' => $vehicle,
                'vehicleNumber' => $vehicle->number,
                'proprietary' => $this->getInfoProprietary($vehicle->proprietary),
                'driver' => $this->getInfoDriver($vehicle->driver),
            ]);
        }

        return $vehicleReport;
    }

    /**
     * @param Proprietary|null $proprietary
     * @return object
     */
    private function getInfoProprietary(Proprietary $proprietary = null)
    {
        return (object)[
            'exists' => $proprietary ? true : false,
            'fullName' => $proprietary ? $proprietary->fullName() : '',
            'identity' => $proprietary ? $proprietary->identity : '',
            'address' => $proprietary ? $proprietary->address : '',
            'email' => $proprietary ? $proprietary->email : '',
            'phone' => $proprietary ? $proprietary->getPhone() : '',
            'details' => $proprietary ? $proprietary->infoDetail() : '',
        ];;
    }

    /**
     * @param Driver|null $driver
     * @return object
     */
    private function getInfoDriver(Driver $driver = null)
    {
        return (object)[
            'exists' => $driver ? true : false,
            'fullName' => $driver ? $driver->fullName() : '',
            'identity' => $driver ? $driver->identity : '',
            'address' => $driver ? $driver->address : '',
            'email' => $driver ? $driver->email : '',
            'phone' => $driver ? $driver->getPhone() : '',
            'details' => $driver ? $driver->infoDetail() : '',
        ];;
    }
}