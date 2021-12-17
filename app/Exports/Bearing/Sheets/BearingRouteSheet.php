<?php

namespace App\Exports\Bearing\Sheets;

use App\Exports\ExportWithPCWStyle;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Collection;
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

class BearingRouteSheet implements FromCollection, ShouldAutoSize, Responsable, WithTitle, WithHeadings, WithEvents, WithColumnFormatting
{
    use ExportWithPCWStyle;

    /**
     * @param Collection $report
     */
    public function __construct($report)
    {
        $dataExcel = array();
        $bearing = $report->get('bearing');
        $date = $report->get('date');

        foreach ($bearing as $b) {
            $b = (object)$b;

            $dataExcel[] = [
                __('Vehicle') => $b->vehicle,
                __('Departure') => $b->departure,
                __('Route') => $b->route['name'],
                __('Duration') => '',
            ];
        }

        $this->report = (object)[
            'fileName' => __('Bearing report'),
            'title' => __('Bearing'),
            'subTitle' => $date,
            'sheetTitle' => __('Bearing') . ' ' . $date,
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
        $workSheet->getStyle('D' . $config->row->data->start . ":" . $totalLetter . $config->row->data->next)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        foreach (range($config->row->data->start + 1, $config->row->data->end) as $row) {
            $prev = $row - 1;
            $value = $workSheet->getCell("B$row")->getValue();
            $prevValue = $workSheet->getCell("B$prev")->getValue();
            if ($value && $prevValue) $workSheet->setCellValue("D$row", "=B$row-B$prev");
        }
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'B' => NumberFormat::FORMAT_DATE_TIME3,
            'D' => NumberFormat::FORMAT_DATE_TIME3,
        ];
    }
}
