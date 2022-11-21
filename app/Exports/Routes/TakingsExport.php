<?php

namespace App\Exports\Routes;

use App\Exports\ExportWithPCWStyle;
use App\Models\Routes\Route;
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

class TakingsExport implements FromCollection, ShouldAutoSize, Responsable, WithTitle, WithHeadings, WithEvents, WithColumnFormatting
{
    use ExportWithPCWStyle;

    /**
     * ConsolidatedDailyExport constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $dataExcel = array();
        $params = $data->params;
        $dataReport = $data->report;
        foreach ($dataReport->report as $report) {
            $observations = $report->takings->observations;

            if (!$report->takings->isTaken && $report->forNormalTakings) {
                $observations = "      << " . strtoupper(__('No taken')) . " >>";
            }

            $route = $report->onlyControlTakings ? __('Takings without dispatch turns') : $report->route->name;
            $user = "";
            $updatedAt = "";
            if ($report->takings->user) {
                $user = trim($report->takings->user->username . ": " . $report->takings->user->name);
                $updatedAt = $report->takings->updatedAt;
            }
            $dataExcel[] = [
                __('N°') => count($dataExcel) + 1,                                                                  # A CELL
                __('Date') => $report->date,                                                                        # B CELL
                __('Vehicle') => $report->vehicle->number,                                                          # C CELL
                __('Driver code') => $report->driverCode,                                                           # D CELL
                __('Route') => $route,                                                                              # E CELL
                __('Round trip') => $report->forNormalTakings ? $report->roundTrip : '',                            # F CELL
                __('Start recorder') => $report->forNormalTakings ? $report->passengers->recorders->start : '',     # G CELL
                __('End recorder') => $report->forNormalTakings ? $report->passengers->recorders->end : '',         # H CELL
                __('Passengers') => $report->forNormalTakings ? $report->passengers->recorders->count : 0,          # I CELL
                __('Total production') => intval($report->takings->totalProduction),                                # J CELL
                __('Control') => intval($report->takings->control),                                                 # K CELL
                __('Fuel') => intval($report->takings->fuel),                                                       # L CELL
                __('Fuel gallons') => number_format($report->takings->fuelGallons, 2),                     # M CELL
                __('Station') => $report->takings->fuelStation->name,                                               # N CELL
                __('Various') => intval($report->takings->bonus),                                                   # O CELL
                __('Others') => intval($report->takings->others),                                                   # P CELL
                __('Net production') => intval($report->takings->netProduction),                                    # Q CELL
                __('Advance') => intval($report->takings->advance),                                                 # R CELL
                __('Passengers taken') => intval($report->takings->passengersTaken),                                # S CELL
                __('Balance') => intval($report->takings->balance),                                                 # T CELL
                __('Passengers balance') => intval($report->takings->passengersBalance),                            # U CELL
                __('Observations') => $observations,                                                                # V CELL
                __('User') => $user,                                                                                # W CELL
                __('Updated at') => $updatedAt,                                                                     # X CELL
                __('Manual total passengers') => $report->takings->manualTotalPassengers,                           # Y CELL
                __('Manual total production') => $report->takings->manualTotalProduction,                           # Z CELL
            ];
        }

        $dateReport = $params->initialDate . ($params->finalDate ? " - $params->finalDate" : '');
        $vehicle = $params->vehicle ? Vehicle::find($params->vehicle)->number : "";
        $route = $params->route ? Route::find($params->route)->name : "";

        $subtitle = $vehicle ? __('Vehicle') . ": $vehicle" : __('All vehicles');
        $subtitle .= " | " . ($route ? __('Route') . ": $route" : __('All routes'));

        $this->report = (object)[
            'fileName' => __('Takings detailed r.') . " $dateReport",
            'title' => __('Takings detailed report') . "\n $dateReport",
            'subTitle' => "$subtitle",
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

        foreach (range($config->row->data->start, $config->row->data->end) as $row) {
            $workSheet->setCellValue("I$row", "=H$row-G$row");
            $workSheet->setCellValue("Q$row", "=J$row-K$row-L$row-O$row-P$row");
            $workSheet->setCellValue("T$row", "=Q$row-R$row");
//            $workSheet->setCellValue("S$row", "=R$row/(J$row/I$row)");
            $workSheet->setCellValue("U$row", "=I$row-S$row");
        }

        $workSheet->setCellValue('H' . $config->row->data->next, 'TOTAL');
        $workSheet->getStyle('H' . $config->row->data->next)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        foreach (['I', 'J', 'K', 'L', 'M', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'Y', 'Z'] as $totalLetterPosition) {
            $workSheet->setCellValue($totalLetterPosition . $config->row->data->next, '=SUM(' . $totalLetterPosition . $config->row->data->start . ':' . $totalLetterPosition . $config->row->data->end . ')');

            $workSheet->getStyle($totalLetterPosition . $config->row->data->next)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD);
            $workSheet->getStyle($totalLetterPosition . $config->row->data->next)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        $workSheet->getStyle('I' . $config->row->data->next)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $workSheet->getStyle('M' . $config->row->data->next)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
        $workSheet->getStyle('S' . $config->row->data->next)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
        $workSheet->getStyle('U' . $config->row->data->next)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

        foreach (['C', 'D', 'F', 'I', 'M'] as $cell) {
            $this->setCenter($workSheet, $cell);
        }
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER,
            'G' => NumberFormat::FORMAT_NUMBER,
            'H' => NumberFormat::FORMAT_NUMBER,
            'J' => NumberFormat::FORMAT_CURRENCY_USD,
            'K' => NumberFormat::FORMAT_CURRENCY_USD,
            'L' => NumberFormat::FORMAT_CURRENCY_USD,
            'M' => NumberFormat::FORMAT_NUMBER_00,
            'O' => NumberFormat::FORMAT_CURRENCY_USD,
            'P' => NumberFormat::FORMAT_CURRENCY_USD,
            'Q' => NumberFormat::FORMAT_CURRENCY_USD,
            'R' => NumberFormat::FORMAT_CURRENCY_USD,
            'S' => NumberFormat::FORMAT_NUMBER_00,
            'T' => NumberFormat::FORMAT_CURRENCY_USD,
            'U' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }

    private function setCenter($workSheet, $cell)
    {
        $config = $this->getConfig();
        $workSheet->getStyle($cell . $config->row->data->start . ':' . $cell . $config->row->data->next)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }
}
