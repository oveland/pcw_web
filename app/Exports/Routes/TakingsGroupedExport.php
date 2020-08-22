<?php

namespace App\Exports\Routes;

use App\Exports\ExportWithPCWStyle;
use App\Exports\Routes\Sheets\VehicleTotalsSheet;
use App\Models\Vehicles\Vehicle;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Excel as Extension;

class TakingsGroupedExport implements WithMultipleSheets
{
    use ExportWithPCWStyle;

    private $data;

    /**
     * ConsolidatedDailyExport constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;

        $params = $this->data->params;
        $from = str_replace('-', '', $params->initialDate);
        $to = ($params->finalDate ? "-" . str_replace('-', '', $params->finalDate) : '');

        $dateReport = $from . $to;
        $this->fileName = Str::limit(__('Takings grouped r.') . " $dateReport", 31, '') . '.' . Extension::XLSX;
    }

    public function sheets(): array
    {
        $sheets = collect([]);
        $params = $this->data->params;
        $reportByVehicles = $this->data->report;

        $reportByVehicles = collect($reportByVehicles)->sortBy(function ($r, $v) {
            return Vehicle::find($v)->number;
        });

        foreach ($reportByVehicles as $vehicleId => $reportByDates) {
            $params->vehicle = $vehicleId;
            $sheets->push(new VehicleTotalsSheet((object)[
                'report' => $reportByDates,
                'params' => $params
            ]));
        }

        return $sheets->toArray();
    }
}
