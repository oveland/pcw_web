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

class TakingsTotalsExport implements FromCollection, ShouldAutoSize, Responsable, WithTitle, WithHeadings, WithEvents, WithColumnFormatting
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
        $reportByVehicles = $data->report;

        foreach ($reportByVehicles as $reportByDates) {
            foreach ($reportByDates as $date => $data) {
                $report = $data->report->sortBy('departureTime');
                $firstDr = $report->first();
                $lastDr = $report->last();
                $roundTrips = $report->max('roundTrip');
                $totals = (object)$data->totals;

                $routesNames = collect($report->pluck('route.name'));
                $routesNames = $routesNames->combine($routesNames)->implode(', ');

                $observations = $totals->observations;
//                if (!$report->takings->isTaken && $report->forNormalTakings) {
//                    $observations = "      << " . strtoupper(__('No taken')) . " >>";
//                }

//                $route = $report->onlyControlTakings ? __('Takings without dispatch turns') : $report->route->name;

                $dataExcel[] = [
                    __('N°') => count($dataExcel) + 1,                                                              # A CELL
                    __('Date') => $date,                                                                            # B CELL
                    __('Vehicle') => $firstDr->vehicle->number,                                                     # C CELL
                    __('Routes') => $routesNames,                                                                   # D CELL
                    __('Round trips') => $roundTrips,                                                               # E CELL
                    __('Start recorder') => $firstDr->passengers->recorders->start,                                 # F CELL
                    __('End recorder') => $lastDr->passengers->recorders->end,                                      # G CELL
                    __('Passengers') => $totals->passengers,                                                        # H CELL
                    __('Total production') => intval($totals->totalProduction),                                     # I CELL
                    __('Control') => intval($totals->control),                                                      # J CELL
                    __('Fuel') => intval($totals->fuel),                                                            # K CELL
                    __('Fuel gallons') => number_format($totals->fuelGallons, 2),                          # L CELL
                    __('Various') => intval($totals->bonus),                                                        # M CELL
                    __('Others') => intval($totals->others),                                                        # N CELL
                    __('Net production') => intval($totals->netProduction),                                         # O CELL
                    __('Observations') => $observations,                                                            # P CELL
                ];
            }
        }

        $dateReport = $params->initialDate . ($params->finalDate ? " - $params->finalDate" : '');
        $vehicle = $params->vehicle ? Vehicle::find($params->vehicle)->number : "";
        $route = $params->route ? Route::find($params->route)->name : "";

        $subtitle = $vehicle ? __('Vehicle') . ": $vehicle" : __('All vehicles');
        $subtitle .= " | " . ($route ? __('Route') . ": $route" : __('All routes'));

        $this->report = (object)[
            'fileName' => __('Takings totals r.') . " $dateReport",
            'title' => __('Takings totals report') . "\n $dateReport",
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
        $this->fillColorTitle = 'ffc92700';
        $this->fillColorSubTitle = 'ffc94100';
        $this->fillColorHeaders = 'ff772100';
        $this->borderColorAll = 'dddddddd';

        $config = $this->getConfig();
        $totalLetter = $config->letter->end;
        $workSheet = $spreadsheet->getDelegate();

        foreach (range($config->row->data->start, $config->row->data->end) as $row) {
            $workSheet->setCellValue("O$row", "=I$row-J$row-K$row-M$row-N$row");
        }

        $workSheet->setCellValue('G' . $config->row->data->next, 'TOTAL');
        $workSheet->getStyle('G' . $config->row->data->next)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        foreach (['H', 'I', 'J', 'K', 'L', 'M', 'N', 'O'] as $totalLetterPosition) {
            $workSheet->setCellValue($totalLetterPosition . $config->row->data->next, '=SUM(' . $totalLetterPosition . $config->row->data->start . ':' . $totalLetterPosition . $config->row->data->end . ')');

            $workSheet->getStyle($totalLetterPosition . $config->row->data->next)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD);
            $workSheet->getStyle($totalLetterPosition . $config->row->data->next)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        $workSheet->getStyle('H' . $config->row->data->next)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $workSheet->getStyle('L' . $config->row->data->next)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

        foreach (['C', 'E', 'H', 'L'] as $cell) {
            $this->setCenter($workSheet, $cell);
        }

        $this->setStyleSheetWithFooter($spreadsheet);
        $this->setGlobalStyleSheet($spreadsheet);
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
            'I' => NumberFormat::FORMAT_CURRENCY_USD,
            'J' => NumberFormat::FORMAT_CURRENCY_USD,
            'K' => NumberFormat::FORMAT_CURRENCY_USD,
            'L' => NumberFormat::FORMAT_NUMBER_00,
            'M' => NumberFormat::FORMAT_CURRENCY_USD,
            'N' => NumberFormat::FORMAT_CURRENCY_USD,
        ];
    }

    private function setCenter($workSheet, $cell)
    {
        $config = $this->getConfig();
        $workSheet->getStyle($cell . $config->row->data->start . ':' . $cell . $config->row->data->next)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }
}
