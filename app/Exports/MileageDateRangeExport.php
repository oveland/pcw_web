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

class MileageDateRangeExport implements FromCollection, ShouldAutoSize, Responsable, WithTitle, WithHeadings, WithEvents, WithColumnFormatting
{
    use ExportWithPCWStyle;

    public function __construct($mileageReport)
    {
        $reports = $mileageReport->reports;
        $dataExcel = collect([]);
        foreach ($reports as $report) {
            $dataExcel->push([
                __('N°') => count($dataExcel) + 1,                      # A CELL
                __('Date') => $report->date,                            # B CELL
                __('Number') => $report->vehicleNumber,                 # C CELL
                __('Plate') => $report->vehiclePlate,                   # D CELL
                __('Mileage') . " (Km)" => "=$report->mileage/1000",    # E CELL
            ]);
        }

        $vehicleNumber = __("for all");
        if ($mileageReport->vehicleReport != 'all' && $dataExcel->count()) {
            $vehicleNumber = __('Vehicle') . " " . $dataExcel->first()[__('Number')];
        }

        $this->report = (object)[
            'fileName' => __('KM') . " $mileageReport->initialDateReport $mileageReport->finalDateReport",
            'title' => __('Mileage report') . "\n$mileageReport->initialDateReport $mileageReport->finalDateReport",
            'subTitle' => __('Mileage') . " $vehicleNumber Total ".number_format($mileageReport->mileageByFleet, '2', ',', '.')." km",
            'sheetTitle' => __('Mileage') . " $vehicleNumber",
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
        $totalLetter = 'E';
        $config = $this->getConfig();
        $workSheet = $spreadsheet->getDelegate();

        $workSheet->getStyle('B' . $config->row->data->start . ":" . $totalLetter . $config->row->data->end)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $workSheet->setCellValue('D' . $config->row->data->next, 'TOTAL');
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
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
        ];
    }
}
