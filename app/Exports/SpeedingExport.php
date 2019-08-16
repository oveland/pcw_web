<?php

namespace App\Exports;

use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SpeedingExport implements FromCollection, ShouldAutoSize, Responsable, WithTitle, WithHeadings, WithEvents, WithColumnFormatting
{
    use ExportWithPCWStyle;

    public function __construct($speedingReport)
    {
        $speedingReportByVehicle = $speedingReport->report;
        $dateReport = $speedingReport->dateReport;
        $typeReport = $speedingReport->typeReport;

        $allSpeedingReport = $speedingReportByVehicle->collapse();
        $dataExcel = collect([]);

        foreach ($allSpeedingReport as $speeding) {
            $vehicle = $speeding->vehicle;
            $speed = $speeding->speed;

            $dataExcel->push([
                __('N°') => count($dataExcel) + 1,                             # A CELL
                __('Time') => $speeding->time->toTimeString(),                 # C CELL
                __('Vehicle') => $vehicle->number,                             # B CELL
                __('Plate') => $vehicle->plate,                                # D CELL
                __('Speed')."\nKm/h" => $speed,# E CELL
                __('Address') => $speeding->getAddress(true)# E CELL
            ]);
        }

        $this->report = (object)[
            'fileName' => __('Speeding') . " $dateReport",
            'title' => " $dateReport",
            'subTitle' => $allSpeedingReport->count() . " " . __('Speeding'),
            'sheetTitle' => __('Speeding') . " $dateReport",
            'data' => $dataExcel
        ];

        $this->setFileName();

        /*if( false && $typeReport == 'group' ){
            Excel::create(__('Speeding') . " $dateReport", function ($excel) use ($speedingReportByVehicle, $dateReport) {
                foreach ($speedingReportByVehicle as $speedingReport) {
                    $vehicle = $speedingReport->first()->vehicle;
                    $dataExcel = array();

                    foreach ($speedingReport as $speeding) {
                        $speed = $speeding->speed;
                        if( $speed > 200 ){
                            $speed = 100 + (random_int(-10,10));
                        }

                        $dataExcel[] = [
                            __('N°') => count($dataExcel) + 1,                             # A CELL
                            __('Time') => $speeding->time->toTimeString(),                                 # B CELL
                            __('Vehicle') => intval($vehicle->number),                     # C CELL
                            __('Plate') => $vehicle->plate,                                # D CELL
                            __('Speed') => number_format($speed,2, ',', ''),# E CELL
                            __('Address') => Geolocation::getAddressFromCoordinates($speeding->latitude, $speeding->longitude)# E CELL
                        ];
                    }

                    $dataExport = (object)[
                        'fileName' => __('Speeding') . " $dateReport",
                        'title' => __('Speeding') . " $dateReport",
                        'subTitle' => count($speedingReport)." ".__('Speeding'),
                        'sheetTitle' => "$vehicle->number",
                        'data' => $dataExcel
                    ];
                    //foreach ()
                    $excel = PCWExporterService::createHeaders($excel, $dataExport);
                    $excel = PCWExporterService::createSheet($excel, $dataExport);
                }
            })->
            export('xlsx');
        }*/
    }

    /**
     * @param Sheet $spreadsheet
     * @throws Exception
     */
    public function setStyleSheet(Sheet $spreadsheet)
    {
        $lastCenterLetter = 'E';
        $config = $this->getConfig();
        $workSheet = $spreadsheet->getDelegate();

        $workSheet->getStyle('B' . $config->row->data->start . ":" . $lastCenterLetter . $config->row->data->end)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER,
        ];
    }
}