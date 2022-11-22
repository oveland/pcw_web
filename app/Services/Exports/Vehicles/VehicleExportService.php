<?php

namespace App\Services\Exports\Vehicles;

use App\Services\Exports\PCWExporterService;
use Carbon\Carbon;
use Excel;

class VehicleExportService
{
    /**
     * @param $vehiclesReport
     * @param bool $store
     * @return string
     */
    public function exportVehicleReport($vehiclesReport, $store = false)
    {
        $now = Carbon::now();
        $fileName = str_replace([' ', '-'], '_', __('Vehicle report') . " ".$now->toDateTimeString());
        $fileExtension = 'xlsx';

        $excelFile = Excel::create($fileName, function ($excel) use ($vehiclesReport, $now) {
            $dataExcel = collect([]);
            foreach ($vehiclesReport as $vehicleReport) {
                $vehicle = $vehicleReport->vehicle;
                $proprietary = $vehicleReport->proprietary;
                $driver = $vehicleReport->driver;

                $data = collect([
                    __('#') => $dataExcel->count() + 1,                                                            # A CELL
                    __('Number') => $vehicle->number,                                                              # B CELL
                    __('Plate') => $vehicle->plate,                                                                # C CELL
                    __('Status') => __($vehicle->active ? 'Active' : 'Inactive'),                             # D CELL
                    __('Proprietary') => $proprietary ? $proprietary->fullName : '',                               # E CELL
                    __('Proprietary info') => $proprietary ? $proprietary->details : '',                           # G CELL
                    __('Driver') => $driver ? $driver->fullName : '',                                              # H CELL
                    __('Driver info') => $driver ? $driver->details : '',                                # I CELL
                ]);

                $dataExcel->push($data->toArray());
            }

            $dataExport = (object)[
                'fileName' => __('Vehicle report') . " ".$now->toDateTimeString(),
                'title' => __('Vehicle report'),
                'subTitle' => __('Date')." ".$now->toDateTimeString(),
                'sheetTitle' => __('Vehicle report'),
                'data' => $dataExcel->toArray(),
                'type' => 'vehicleReport'
            ];

            /* SHEETS */
            $excel = PCWExporterService::createHeaders($excel, $dataExport);
            $excel = PCWExporterService::createSheet($excel, $dataExport);
        });

        if ($store) {
            $excelFile->store($fileExtension);
            return "$excelFile->storagePath/$fileName.$fileExtension";
        }

        return $excelFile->export($fileExtension);
    }
}