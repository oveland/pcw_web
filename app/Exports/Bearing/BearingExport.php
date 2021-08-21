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

        $this->report = collect($data->report);

        $this->fileName = Str::limit(__('R.') . " $data->routeName $data->date", 31, '') . '.' . Extension::XLSX;
    }

    public function sheets(): array
    {
        return $this->report->map(function ($report) {
            return new BearingRouteSheet((object)$report);
        })->toArray();
    }
}
