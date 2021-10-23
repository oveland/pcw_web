<?php

namespace App\Exports\LM;

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
use function __;
use function collect;

class DailyReportExport implements FromCollection, ShouldAutoSize, Responsable, WithTitle, WithHeadings, WithEvents, WithColumnFormatting
{
    use ExportWithPCWStyle;

    public function __construct($report)
    {
        $data = $report->data;
        $dataExcel = collect([]);

        if ($data->isEmpty()) {
            throw new \Exception("Report data is empty");
        }

        $markWithOtherDiscounts = collect([]);

        foreach ($data as $d) {
            $mark = $d->mark;
            $trajectory = $mark->trajectory;
            $liquidationTurn = $d->liquidationTurn;
            $liquidationDetails = $d->liquidationDetails;
            $turn = $mark->turn;
            $vehicle = (object)$turn->vehicle;

            $otherDiscounts = collect($liquidationDetails->otherDiscounts)->sum('value');

            if ($markWithOtherDiscounts->get($mark->liquidation_id)) {
                $otherDiscounts = 0;
            } else {
                $markWithOtherDiscounts->put($mark->liquidation_id, $otherDiscounts);
            }

            $totalTaken = $mark->taken ? $liquidationTurn->totalDispatch - $otherDiscounts : 0;

            $dataExcel->push([
                __('N°') => count($dataExcel) + 1,                                                                      # A CELL
                __('Trajectory') => $trajectory->name,                                                                  # B CELL
                __('Time') => $mark->initialTime,                                                                       # C CELL
                __('Vehicle') => $vehicle->number,                                                                      # D CELL
                __('Total Gross LM') => $mark->totalGrossBEA,                                                          # E CELL
                __('Passengers') => $mark->passengersBEA,                                                               # F CELL
                __('Total liquidated') => $liquidationTurn->totalDispatch,                                              # G CELL
                __('Other discounts') => $otherDiscounts,                                                               # H CELL
                __('Total taken') => $totalTaken,                                                                       # I CELL
                __('Difference') => $liquidationTurn->totalDispatch - $otherDiscounts - $totalTaken,                    # J CELL
            ]);
        }

        $this->report = (object)[
            'fileName' => __('ML') . "_$report->date",
            'title' => __('Consolidated daily'),
            'subTitle' => $report->date,
            'sheetTitle' => $report->date,
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

        $workSheet->getStyle('B' . $config->row->data->start . ":" . $totalLetter . $config->row->data->next)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $workSheet->getStyle('E' . $config->row->data->start . ":" . "E" . $config->row->data->next)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $workSheet->getStyle('G' . $config->row->data->start . ":" . $totalLetter . $config->row->data->next)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        foreach (range($config->row->data->start, $config->row->data->end) as $row) {
            $workSheet->setCellValue("J$row", "=G$row-H$row-I$row");
        }

        $workSheet->setCellValue('D' . $config->row->data->next, 'TOTAL');
        $workSheet->setCellValue("E" . $config->row->data->next, '=SUM(' . "E" . $config->row->data->start . ':' . "E" . $config->row->data->end . ')');
        $workSheet->setCellValue("F" . $config->row->data->next, '=SUM(' . "F" . $config->row->data->start . ':' . "F" . $config->row->data->end . ')');
        $workSheet->setCellValue("G" . $config->row->data->next, '=SUM(' . "G" . $config->row->data->start . ':' . "G" . $config->row->data->end . ')');
        $workSheet->setCellValue("H" . $config->row->data->next, '=SUM(' . "H" . $config->row->data->start . ':' . "H" . $config->row->data->end . ')');
        $workSheet->setCellValue("I" . $config->row->data->next, '=SUM(' . "I" . $config->row->data->start . ':' . "I" . $config->row->data->end . ')');
        $workSheet->setCellValue("J" . $config->row->data->next, '=SUM(' . "J" . $config->row->data->start . ':' . "J" . $config->row->data->end . ')');

        $workSheet->getStyle('E' . $config->row->data->next . ":" . "E" . $config->row->data->next)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD);
        $workSheet->getStyle('G' . $config->row->data->next . ":" . $totalLetter . $config->row->data->next)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD);
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        $config = $this->getConfig();
        return [
            'E' => NumberFormat::FORMAT_CURRENCY_USD,
            'G' => NumberFormat::FORMAT_CURRENCY_USD,
            'H' => NumberFormat::FORMAT_CURRENCY_USD,
            'I' => NumberFormat::FORMAT_CURRENCY_USD,
            'J' => NumberFormat::FORMAT_CURRENCY_USD,
        ];
    }
}
