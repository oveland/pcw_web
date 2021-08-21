<?php

namespace App\Exports\Bearing\Sheets;

use App\Exports\ExportWithPCWStyle;
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

class BearingRouteSheet implements FromCollection, ShouldAutoSize, Responsable, WithTitle, WithHeadings, WithEvents, WithColumnFormatting
{
    use ExportWithPCWStyle;

    public function __construct($report)
    {
        $dataExcel = array();
        $route = (object)$report->route;
        $bearing = (object)$report->bearing;
        $date = $report->date ?? Carbon::now()->toDateString();

        foreach ($bearing as $b) {
            $b = (object)$b;

            $dataExcel[] = [
                __('Turn') => $b->turn,
                __('Vehicle') => $b->vehicle,
                __('Departure') => $b->departure,
                __('Arrival') => $b->arrival,
            ];
        }

        $this->report = (object)[
            'fileName' => __('Bearing report'),
            'title' => __('Bearing') . "\n" . $route->name,
            'subTitle' => $date,
            'sheetTitle' => $route->name,
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
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
        ];
    }
}
