<?php


namespace App\Exports;


use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Excel as Extension;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

trait ExportWithPCWStyle
{
    use Exportable;

    private $report;

    /**
     * It's required to define the fileName within
     * the export class when making use of Responsable.
     */
    public $fileName = "export.".Extension::XLSX;


    /**
     * Optional headers
     */
    private $headers = [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];

    /**
     * Optional Writer Type
     */
    private $writerType = Extension::XLSX;

    private static $fillColorTitle = 'ff01142c';
    private static $fillColorSubTitle = 'ff01042c';
    private static $fillColorHeaders = 'ff021833';
    private static $borderColorAll = 'dddddddd';

    function setFileName()
    {
        $this->fileName = Str::limit($this->report->fileName, 28) . '.' . Extension::XLSX;
    }

    /**
     * Set teh sheet title
     * @return string
     */
    public function title(): string
    {
        return Str::limit($this->report->sheetTitle, 28);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        $firstData = $this->collection()->first();
        return array_keys($firstData ? $firstData : []);
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return collect($this->report->data);
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $event->sheet->getDelegate()->setCellValue('A1', $this->report->title);
                $event->sheet->getDelegate()->setCellValue('A2', $this->report->subTitle);
            },
            AfterSheet::class => function (AfterSheet $event) {
                $this->setGlobalStyleSheet($event->sheet);
                $this->setStyleSheet($event->sheet);
                $event->sheet->getDelegate()->getStyle('A1');
            },
        ];
    }

    /**
     * @param Sheet $spreadsheet
     */
    public function setStyleSheet(Sheet $spreadsheet)
    {
    }

    /**
     * @param Sheet $spreadsheet
     * @throws Exception
     */
    public function setStyleSheetWithFooter(Sheet $spreadsheet)
    {
        $config = $this->getConfig();
        $workSheet = $spreadsheet->getDelegate();

        $workSheet->getStyle($config->cellRange->lastDataRow)->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => self::$fillColorTitle]],
            'font' => ['color' => ['argb' => Color::COLOR_WHITE], 'bold' => true]
        ]);
    }

    /**
     * @param Sheet $spreadsheet
     * @throws Exception
     */
    public function setGlobalStyleSheet(Sheet $spreadsheet)
    {
        $config = $this->getConfig();
        $workSheet = $spreadsheet->getDelegate();

        $workSheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->mergeCells($config->cellRange->title);
        $spreadsheet->mergeCells($config->cellRange->subTitle);
        $spreadsheet->setAutoFilter($config->cellRange->dataWithHeaders);
        $spreadsheet->getRowDimension($config->row->title)->setRowHeight(40);
        $spreadsheet->getRowDimension($config->row->subTitle)->setRowHeight(20);
        $spreadsheet->getRowDimension($config->row->headers)->setRowHeight(40);

        $styleAll = $workSheet->getStyle($config->cellRange->all);
        $styleAll->getAlignment()->setWrapText(true);
        $styleAll->applyFromArray([
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['argb' => self::$borderColorAll]]],
            'font' => ['name' => 'Consolas', 'size' => 10],
        ]);

        $workSheet->getStyle($config->cellRange->title)->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['argb' => self::$borderColorAll]]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => self::$fillColorTitle]],
            'font' => ['color' => ['argb' => Color::COLOR_WHITE], 'bold' => true]
        ]);

        $workSheet->getStyle($config->cellRange->subTitle)->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['argb' => self::$borderColorAll]]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => self::$fillColorSubTitle]],
            'font' => ['color' => ['argb' => Color::COLOR_WHITE], 'bold' => true]
        ]);

        $workSheet->getStyle($config->cellRange->headers)->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['argb' => self::$borderColorAll]]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => self::$fillColorHeaders]],
            'font' => ['color' => ['argb' => Color::COLOR_WHITE], 'bold' => true]
        ]);

        $workSheet->getStyle($config->cellRange->firstDataColumn)->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => self::$fillColorHeaders]],
            'font' => ['color' => ['argb' => Color::COLOR_WHITE], 'bold' => true]
        ]);
    }

    /**
     * @return object
     */
    function getConfig()
    {
        $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '...'];

        /* ROWS */
        $titleRow = 1;
        $subTitleRow = 2;
        $headersRow = 3;
        $startDataRow = 4;
        $endDataRow = $this->collection()->count() + $startDataRow - 1;
        $nextDataRow = $endDataRow + 1;

        /*COLUMNS*/
        $totalColumns = count(array_keys($this->collection()->first())) - 1;

        /* LETTERS */
        $startDataLetter = 'A';
        $endDataLetter = $letters[$totalColumns];

        return (object)[
            'row' => (object)[
                'title' => $titleRow,
                'subTitle' => $subTitleRow,
                'headers' => $headersRow,
                'data' => (object)[
                    'start' => $startDataRow,
                    'end' => $endDataRow,
                    'next' => $nextDataRow
                ]
            ],
            'totalColumns' => $totalColumns,
            'letter' => (object)[
                'start' => $startDataLetter,
                'end' => $endDataLetter
            ],
            'cellRange' => (object)[
                'title' => $startDataLetter . $titleRow . ":" . $endDataLetter . $titleRow,
                'subTitle' => $startDataLetter . $subTitleRow . ":" . $endDataLetter . $subTitleRow,
                'headers' => $startDataLetter . $headersRow . ":" . $endDataLetter . $headersRow,
                'data' => $startDataLetter . $startDataRow . ":" . $endDataLetter . $endDataRow,
                'dataWithHeaders' => $startDataLetter . $headersRow . ":" . $endDataLetter . $endDataRow,
                'firstDataColumn' => $startDataLetter . $startDataRow . ":" . $startDataLetter . $endDataRow,
                'lastDataRow' => $startDataLetter . $nextDataRow . ":" . $endDataLetter . $nextDataRow,
                'all' => $startDataLetter . $titleRow . ":" . $endDataLetter . $endDataRow,
            ]
        ];
    }
}