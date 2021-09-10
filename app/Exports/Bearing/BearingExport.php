<?php

namespace App\Exports\Bearing;

use App\Exports\Bearing\Sheets\BearingRouteSheet;
use App\Exports\ExportWithPCWStyle;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Excel as Extension;

class BearingExport implements WithMultipleSheets
{
    use ExportWithPCWStyle;

    private $report;

    public function __construct($data)
    {
        $data = (object)$data;

        $this->report = collect([
            'date' => $data->date,
            'bearing' => collect([])
        ]);

        collect($data->bearing)->groupBy('vehicle')->each(function ($bearingByVehicle) {
            collect($bearingByVehicle)->sortBy('departure')->each(function ($bearing) {
                $this->report->get('bearing')->push($bearing);
            });

            $this->report->get('bearing')->push([
                'vehicle' => '',
                'departure' => '',
                'turn' => '',
                'route' => [
                    'name' => ''
                ],
                'arrival' => '',
            ]);
        });

        $this->fileName = Str::limit(__('R.') . " $data->date", 31, '') . '.' . Extension::XLSX;
    }

    public function sheets(): array
    {
        return [new BearingRouteSheet((object)$this->report)];
    }
}
