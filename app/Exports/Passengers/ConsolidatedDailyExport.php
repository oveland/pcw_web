<?php

namespace App\Exports\Passengers;

use App\Exports\ExportWithPCWStyle;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
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

class ConsolidatedDailyExport implements FromCollection, ShouldAutoSize, Responsable, WithTitle, WithHeadings, WithEvents, WithColumnFormatting
{
    use ExportWithPCWStyle;

    /**
     * ConsolidatedDailyExport constructor.
     * @param $passengerReports
     */
    public function __construct($passengerReports)
    {
        $dateReport = $passengerReports->date;

        $dataExcel = array();
        foreach ($passengerReports->reports as $report) {
            $vehicle = $report->vehicle;
            $sensor = $report->passengers->sensor;
            $recorder = $report->passengers->recorder;
            $sensorRecorder = $report->passengers->sensorRecorder;

            $totalRoundTrips = 0;
            $detailedRoutes = "";
            foreach ($report->historyRoutesByRecorder as $routeId => $historyRecorder) {
                $ln = $totalRoundTrips > 0 ? "\n" : "";
                $lastHistory = $historyRecorder->last();
                $totalRoundTrips += $lastHistory->roundTrip;
                $detailedRoutes .= "$ln$lastHistory->routeName : $lastHistory->roundTrip " . __('round trips');
            }

            $dataExcel[] = [
                __('N°') => count($dataExcel) + 1,                                      # A CELL
                __('Vehicle') => intval($vehicle->number),                              # B CELL
                __('Plate') => $vehicle->plate,                                         # C CELL
                __('Routes') => $detailedRoutes,                                        # D CELL
                __('Round trips') => $totalRoundTrips,                                  # E CELL
                __('Sensor recorder') => intval($sensorRecorder),                       # F CELL
                __('Recorder') => intval($recorder),                                    # G CELL
                __('Sensor') => intval($sensor),                                        # H CELL
            ];
        }

        $this->report = (object) [
            'fileName' => __('Passengers report') . " $dateReport",
            'title' => __('Passengers')."\n".__('Consolidated per day'),
            'subTitle' => Carbon::createFromFormat('Y-m-d', $passengerReports->date)->format('d-m-Y'),
            'sheetTitle' => $dateReport,
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
        $this->setStyleSheetWithFooter($spreadsheet);

        $config = $this->getConfig();
        $totalLetter = $config->letter->end;
        $workSheet = $spreadsheet->getDelegate();

        $workSheet->getStyle('A' . $config->row->data->start . ":" . "C" . $config->row->data->next)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $workSheet->getStyle('E' . $config->row->data->start . ":" . $totalLetter . $config->row->data->next)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $workSheet->setCellValue('D' . $config->row->data->next, 'TOTAL');
        foreach (['E', 'F', 'G', 'H'] as $totalLetterPosition) {
            $workSheet->setCellValue($totalLetterPosition . $config->row->data->next, '=SUM(' . $totalLetterPosition . $config->row->data->start . ':' . $totalLetterPosition . $config->row->data->end . ')');
        }
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER,
            'F' => NumberFormat::FORMAT_NUMBER,
            'G' => NumberFormat::FORMAT_NUMBER,
            'H' => NumberFormat::FORMAT_NUMBER,
        ];
    }
}
