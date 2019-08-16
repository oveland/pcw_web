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

class HistoricRouteExport implements FromCollection, ShouldAutoSize, Responsable, WithTitle, WithHeadings, WithEvents, WithColumnFormatting
{
    use ExportWithPCWStyle;

    public function __construct($report)
    {
        $dataExcel = collect([]);
        foreach ($report->historic as $location) {
            $infoRoute = $this->getInfoRoute($location);

            $dataExcel->push([
                __('N°') => count($dataExcel) + 1,                                                                  # A CELL
                __('Time') => $location->time,                                                                      # B CELL
                __('Mileage') => $location->currentMileage,                                                         # C CELL
                __('Speed') => $location->speed,                                                                    # D CELL
                __('Exc.') => $location->speeding ? __('YES') : __('NO'),                                 # E CELL
                __('Vehicle status') => $location->vehicleStatus ? $location->vehicleStatus->status : '...',        # F CELL
                __('Address') => $location->address,                                                                # G CELL
                __('Info route') => $infoRoute                                                                      # H CELL
            ]);
        }

        $this->report = (object)[
            'fileName' => __('Historic') . " " . $report->vehicle->number . " $report->dateReport",
            'title' => __('Historic') . " $report->dateReport - #" . $report->vehicle->number,
            'subTitle' => __('Time') . " $report->initialTime - $report->finalTime ",
            'sheetTitle' => __('Historic') . " " . $report->vehicle->number,
            'data' => $dataExcel
        ];

        $this->setFileName();
    }

    /**
     * @param Sheet $spreadsheet
     * @throws Exception
     */
    public function setStyleSheet(Sheet $spreadsheet)
    {
        $lastCenterLetter = 'F';
        $config = $this->getConfig();
        $workSheet = $spreadsheet->getDelegate();

        $workSheet->getStyle('B' . $config->row->data->start . ":" . $lastCenterLetter . $config->row->data->end)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    }

    /**
     * @param $reportLocation
     * @return string
     */
    public function getInfoRoute($reportLocation)
    {
        $infoDispatchRegister = "";
        $dispatchRegister = $reportLocation->dispatchRegister;

        if ($dispatchRegister) {
            $route = $dispatchRegister->route;
            $infoDispatchRegister = "$route->name \n " . __('Round trip') . " $dispatchRegister->round_trip \n " . __('Turn') . " $dispatchRegister->turn \n " . __('Dispatched') . " $dispatchRegister->departure_time \n " . __('Driver') . " $dispatchRegister->driver_name";
        }

        return $infoDispatchRegister;
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_NUMBER,
        ];
    }
}
