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

class RouteRoundTripsExport implements FromCollection, ShouldAutoSize, Responsable, WithTitle, WithHeadings, WithEvents, WithColumnFormatting
{
    use ExportWithPCWStyle;

    public function __construct($roundTripsReport)
    {
        $dateReport = $roundTripsReport->dateReport;
        $reports = $roundTripsReport->reports;

        $dataExcel = collect([]);
        foreach ($reports as $report) {
            $vehicle = $report->vehicle;
            $dataExcel->push([
                __('N°') => count($dataExcel) + 1,                                      # A CELL
                __('Vehicle') => intval($vehicle->number),                              # B CELL
                __('Plate') => $vehicle->plate,                                         # C CELL
                __('Round trips') => intval($report->totalRoundTrips),                  # D CELL
            ]);
        }

        $this->report = (object)[
            'fileName' => __('Round trip report') . " $dateReport",
            'title' => __('Round trip report'),
            'subTitle' => $dateReport,
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
        $totalLetter = 'D';
        $config = $this->getConfig();
        $workSheet = $spreadsheet->getDelegate();

        $workSheet->getStyle('B' . $config->row->data->start . ":" . $totalLetter . $config->row->data->end)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $workSheet->setCellValue('B' . $config->row->data->next, 'TOTAL');
        $workSheet->setCellValue($totalLetter . $config->row->data->next, '=SUM(' . $totalLetter . $config->row->data->start . ':' . $totalLetter . $config->row->data->end . ')');
        $workSheet->getCell($totalLetter . $config->row->data->next)->getStyle()->setQuotePrefix(true);

        $this->setStyleSheetWithFooter($spreadsheet);
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
