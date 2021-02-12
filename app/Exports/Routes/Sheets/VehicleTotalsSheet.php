<?php

namespace App\Exports\Routes\Sheets;

use App\Exports\ExportWithPCWStyle;
use App\Models\Routes\Route;
use App\Models\Vehicles\Vehicle;
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

class VehicleTotalsSheet implements FromCollection, ShouldAutoSize, Responsable, WithTitle, WithHeadings, WithEvents, WithColumnFormatting
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
        $reportByDates = $data->report;

        foreach ($reportByDates as $date => $data) {
            $report = $data->report->sortBy('departureTime');
            $firstDr = $report->first();
            $lastDr = $report->last();

            $roundTrips = $lastDr->onlyControlTakings ? '' : $report->count();
            $totals = (object)$data->totals;

            $routesNames = collect($report->pluck('route.name'));
            $routesNames = $routesNames->combine($routesNames)->implode(', ');

            $observations = $totals->observations;

            $dataExcel[] = [
                __('N°') => count($dataExcel) + 1,                                                              # A CELL
                __('Date') => $date,                                                                            # B CELL
                __('Vehicle') => $firstDr->vehicle->number,                                                     # C CELL
                __('Driver code') => $firstDr->driverCode,                                                      # E CELL
                __('Routes') => $routesNames,                                                                   # F CELL
                __('Round trips') => $roundTrips,                                                               # G CELL
                __('Start recorder') => $firstDr->passengers->recorders->start,                                 # H CELL
                __('End recorder') => $lastDr->passengers->recorders->end,                                      # I CELL
                __('Passengers') => $totals->passengers->recorders->count,                                                        # J CELL
                __('Total production') => intval($totals->totalProduction),                                     # K CELL
                __('Control') => intval($totals->control),                                                      # L CELL
                __('Fuel') => intval($totals->fuel),                                                            # M CELL
                __('Fuel gallons') => number_format($totals->fuelGallons, 2),                          # N CELL
                __('Various') => intval($totals->bonus),                                                        # O CELL
                __('Others') => intval($totals->others),                                                        # P CELL
                __('Net production') => intval($totals->netProduction),                                         # Q CELL
                __('Observations') => $observations,                                                            # R CELL
            ];
        }

        $dateReport = $params->initialDate . ($params->finalDate ? " - $params->finalDate" : '');
        $vehicle = $params->vehicle ? Vehicle::find($params->vehicle)->number : "";
        $route = $params->route ? Route::find($params->route)->name : "";

        $subtitle = __('Vehicle') . ": $vehicle  | " . ($route ? __('Route') . ": $route" : __('All routes'));

        $this->report = (object)[
            'title' => __('Takings grouped report') . "\n $dateReport",
            'subTitle' => "$subtitle",
            'sheetTitle' => $vehicle,
            'data' => $dataExcel
        ];
    }

    /**
     * @param Sheet $spreadsheet
     * @throws Exception
     */
    public function setStyleSheet(Sheet $spreadsheet)
    {
        $this->fillColorTitle = 'ffc92700';
        $this->fillColorSubTitle = 'ffc94100';
        $this->fillColorHeaders = 'ff772100';
        $this->borderColorAll = 'dddddddd';

        $config = $this->getConfig();
        $totalLetter = $config->letter->end;
        $workSheet = $spreadsheet->getDelegate();

        foreach (range($config->row->data->start, $config->row->data->end) as $row) {
            $workSheet->setCellValue("P$row", "=J$row-K$row-L$row-N$row-O$row");
        }

        $workSheet->setCellValue('H' . $config->row->data->next, 'TOTAL');
        $workSheet->getStyle('H' . $config->row->data->next)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        foreach (['I', 'J', 'K', 'L', 'M', 'N', 'O', 'P'] as $totalLetterPosition) {
            $workSheet->setCellValue($totalLetterPosition . $config->row->data->next, '=SUM(' . $totalLetterPosition . $config->row->data->start . ':' . $totalLetterPosition . $config->row->data->end . ')');

            $workSheet->getStyle($totalLetterPosition . $config->row->data->next)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD);
            $workSheet->getStyle($totalLetterPosition . $config->row->data->next)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        $workSheet->getStyle('I' . $config->row->data->next)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $workSheet->getStyle('M' . $config->row->data->next)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

        $this->setStyleSheetWithFooter($spreadsheet);
        $this->setGlobalStyleSheet($spreadsheet);

        foreach (['C', 'D', 'F', 'I', 'M'] as $cell) {
            $this->setCenter($workSheet, $cell);
        }

        foreach (['J', 'K', 'L', 'N', 'O', 'P'] as $cell) {
            $this->setRight($workSheet, $cell);
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
            'N' => NumberFormat::FORMAT_CURRENCY_USD,
            'O' => NumberFormat::FORMAT_CURRENCY_USD,
            'P' => NumberFormat::FORMAT_CURRENCY_USD,
        ];
    }

    private function setCenter($workSheet, $cell)
    {
        $config = $this->getConfig();
        $workSheet->getStyle($cell . $config->row->data->start . ':' . $cell . $config->row->data->next)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    private function setRight($workSheet, $cell)
    {
        $config = $this->getConfig();
        $workSheet->getStyle($cell . $config->row->data->next)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    }
}
