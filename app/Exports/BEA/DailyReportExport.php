<?php

namespace App\Exports\BEA;

use App\Exports\ExportWithPCWStyle;
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

class DailyReportExport implements FromCollection, ShouldAutoSize, Responsable, WithTitle, WithHeadings, WithEvents, WithColumnFormatting
{
    use ExportWithPCWStyle;

    public function __construct($report)
    {
        $data = $report->data;
        $dataExcel = collect([]);

        if( $data->isEmpty() ){
            throw new \Exception("Report data is empty");
        }

        $markWithOtherDiscounts = collect([]);

        foreach ($data as $d) {
            $mark = $d->mark;
            $trajectory = $mark->trajectory;
            $liquidationTurn = $d->liquidationTurn;
            $liquidationDetails = $d->liquidationDetails;
            $turn = $mark->turn;
            $vehicle = (object) $turn->vehicle;

            $otherDiscounts = collect($liquidationDetails->otherDiscounts)->sum('value');

            if($markWithOtherDiscounts->get($mark->liquidation_id)){
                $otherDiscounts = 0;
            }else{
                $markWithOtherDiscounts->put($mark->liquidation_id, $otherDiscounts);
            }

            $dataExcel->push([
                __('N°') => count($dataExcel) + 1,                                                                      # A CELL
                __('Trajectory') => $trajectory->name,                                                                  # B CELL
                __('Time') => $mark->initialTime,                                                                       # C CELL
                __('Vehicle') => $vehicle->number,                                                                      # D CELL
                __('Total Gross BEA') => $mark->totalGrossBEA,                                                          # E CELL
                __('Passengers') => $mark->passengersBEA,                                                               # F CELL
                __('Total turn') => $liquidationTurn->totalTurn,                                                        # G CELL
                __('Other discounts') => $otherDiscounts,                                                               # H CELL
                __('Total dispatch') => $liquidationTurn->totalDispatch,                                                # I CELL
                __('Difference') => $liquidationTurn->totalTurn - $otherDiscounts - $liquidationTurn->totalDispatch,    # J CELL
            ]);
        }

        $this->report = (object)[
            'fileName' => __('ML') . "_$report->date",
            'title' => __('ML') . " $report->date",
            'subTitle' => __('ML') . " $report->date",
            'sheetTitle' => __('ML') . " $report->date",
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
        $config = $this->getConfig();
        $totalLetter = $config->letter->end;
        $workSheet = $spreadsheet->getDelegate();

        $workSheet->getStyle('B' . $config->row->data->start . ":" . $totalLetter . $config->row->data->end)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $workSheet->getStyle('E' . $config->row->data->start . ":" . "E" . $config->row->data->end)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $workSheet->getStyle('G' . $config->row->data->start . ":" . $totalLetter . $config->row->data->end)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $this->setStyleSheetWithFooter($spreadsheet);
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_CURRENCY_USD,
            'G' => NumberFormat::FORMAT_CURRENCY_USD,
            'H' => NumberFormat::FORMAT_CURRENCY_USD,
            'I' => NumberFormat::FORMAT_CURRENCY_USD,
            'J' => NumberFormat::FORMAT_CURRENCY_USD,
        ];
    }
}
