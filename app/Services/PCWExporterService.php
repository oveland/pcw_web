<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 3/10/2017
 * Time: 4:58 PM
 */

namespace App\Services;

use Excel;

class PCWExporterService
{
    public static $fontStyle = 'Segoe UI Light';
    public static $fontColorInverse = '#eeeeee';
    public static $fontColorLink = '#0d0dff';

    /**
     * General exporter table from view
     *
     * @param $dataExport
     */
    public static function excel($dataExport)
    {
        $dataExport = (object)$dataExport;

        Excel::create($dataExport->fileName, function ($excel) use ($dataExport) {
            /* SHEETS */
            $excel = self::createHeaders($excel, $dataExport);
            $excel = self::createSheet($excel, $dataExport);
        })->export('xlsx');
    }

    /**
     * Save excel file and returns storage path
     *
     * @param $dataExport
     * @return string
     */
    public static function store($dataExport)
    {
        $dataExport = (object)$dataExport;
        $fileName = str_replace([' ', '-'], '_', $dataExport->fileName);
        $fileExtension = 'xlsx';
        $excel = Excel::create($fileName, function ($excel) use ($dataExport) {
            /* SHEETS */
            $excel = self::createHeaders($excel, $dataExport);
            $excel = self::createSheet($excel, $dataExport);
        })->store($fileExtension);

        return "$excel->storagePath/$fileName.$fileExtension";
    }

    public static function createHeaders($excel, $dataExport)
    {
        /* INFO DOCUMENT */
        $excel->setTitle($dataExport->title);
        $excel->setCreator(__('PCW Ditech Integradores Tecnológicos'))->setCompany(__('PCW Ditech Integradores Tecnológicos'));
        $excel->setDescription($dataExport->subTitle);

        return $excel;
    }

    public static function createSheet($excel, $dataExport)
    {
        $sheetTitle = isset($dataExport->sheetTitle) ? $dataExport->sheetTitle : $dataExport->subTitle;
        return $excel->sheet($sheetTitle, function ($sheet) use ($dataExport) {
            $startIndex = 3;
            $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '...'];

            $config = (object)[
                'type' => isset($dataExport->type) ? $dataExport->type : null,
                'startIndex' => $startIndex,
                'lastLetter' => $letters[count(array_keys($dataExport->data[0])) - 1],
                'totalRows' => count($dataExport->data) + $startIndex,
            ];

            $sheet->setCellValue('A1', $dataExport->title);
            $sheet->setCellValue('A2', $dataExport->subTitle);
            $sheet->fromArray($dataExport->data, null, 'A3', true, true);

            $sheet = self::sheetCustomReport($sheet, $config);

            /* GENEREAL STYLE */
            $sheet->setOrientation('landscape');
            $sheet->setFontFamily(self::$fontStyle);

            // Set auto size for sheet
            $sheet->getStyle('A1:' . $config->lastLetter . $config->totalRows)->getAlignment()->setWrapText(true);

            $sheet->setBorder("A1:$config->lastLetter" . $config->totalRows, 'thin');
            $sheet->cells("A1:$config->lastLetter" . $config->totalRows, function ($cells) {
                $cells->setFontFamily(self::$fontStyle);
                $cells->setValignment('center');
            });

            /* SORTABLE COLUMN HEADERS */
            $sheet->setAutoFilter("A$startIndex:$config->lastLetter" . ($config->totalRows));

            /*  MAIN HEADER */
            $sheet->setHeight(1, 50);
            $sheet->mergeCells('A1:' . $config->lastLetter . '1');
            $sheet->cells('A1:' . $config->lastLetter . '1', function ($cells) {
                $cells->setValignment('center');
                $cells->setAlignment('center');
                $cells->setBackground('#0e6d62');
                $cells->setFontColor(self::$fontColorInverse);
                $cells->setFont(array(
                    'family' => self::$fontStyle,
                    'size' => '14',
                    'bold' => true
                ));
            });

            /* INFO HEADER */
            $sheet->setHeight(2, 25);
            $sheet->mergeCells('A2:' . $config->lastLetter . '2');
            $sheet->cells('A2:' . $config->lastLetter . '2', function ($cells) {
                $cells->setValignment('center');
                $cells->setAlignment('center');
                $cells->setBackground('#0d4841');
                $cells->setFontColor(self::$fontColorInverse);
                $cells->setFont(array(
                    'family' => self::$fontStyle,
                    'size' => '12',
                    'bold' => true
                ));
            });

            /* HEADER COLUMNS */
            $sheet->setHeight($startIndex, 40);
            $sheet->cells('A' . $startIndex . ':' . $config->lastLetter . $startIndex, function ($cells) {
                $cells->setValignment('center');
                $cells->setAlignment('center');
                $cells->setBackground('#0d4841');
                $cells->setFontColor(self::$fontColorInverse);
                $cells->setFont(array(
                    'family' => self::$fontStyle,
                    'size' => '12',
                    'bold' => true
                ));
            });
        });
    }

    public static function sheetCustomReport($sheet, $config)
    {
        $lastRow = $config->totalRows + 1;
        $starData = $config->startIndex + 1;

        switch ($config->type) {
            case 'passengerReportTotalFooter':
                // Set general formulas
                foreach (['E', 'F', 'G', 'H'] as $totalLetterPosition) {
                    $sheet->setCellValue($totalLetterPosition . $lastRow, "=SUM($totalLetterPosition$starData:$totalLetterPosition$config->totalRows)");
                }

                $sheet->setCellValue("A$lastRow", "TOTAL");
                $sheet = self::styleFooter($sheet, $config);

                $sheet->cells("A$config->startIndex:C$config->totalRows", function ($cells) { $cells->setAlignment('center'); });
                $sheet->cells("E$config->startIndex:H$config->totalRows", function ($cells) { $cells->setAlignment('center'); });
                break;

            case 'passengerReportByRangeTotalFooter':
                foreach (['C','D','E'] as $totalLetterPosition) {
                    $sheet->setCellValue($totalLetterPosition . $lastRow, "=SUM($totalLetterPosition$starData:$totalLetterPosition$config->totalRows)");
                }

                $sheet->setCellValue("A$lastRow", "TOTAL");
                $sheet = self::styleFooter($sheet, $config);
                break;

            case 'routeReportByVehicle':
                // Set general formulas
                for ($i = $starData; $i < $lastRow; $i++) {
                    $sheet->setCellValue("M$i", "=L$i-K$i");
                    $sheet->setCellValue("N$i", "=M$i+" . (($i > $starData) ? ("N" . ($i - 1)) : "0"));
                }
                break;

            case 'passengersReportByRoute':
                // Set general formulas
                foreach (['D'] as $totalLetterPosition) {
                    $sheet->setCellValue($totalLetterPosition . $lastRow, "=SUM($totalLetterPosition$starData:$totalLetterPosition$config->totalRows)");
                }

                $sheet->setCellValue("C$lastRow", "TOTAL");
                $sheet = self::styleFooter($sheet, $config);
                break;

            case 'roundTripsVehicleReport':
                // Set general formulas
                foreach (['D'] as $totalLetterPosition) {
                    $sheet->setCellValue($totalLetterPosition . $lastRow, "=SUM($totalLetterPosition$starData:$totalLetterPosition$config->totalRows)");
                }

                $sheet->cells("A$config->startIndex:" . $config->lastLetter . $lastRow, function ($cells) {
                    $cells->setValignment('center');
                    $cells->setAlignment('center');
                });

                $sheet->setCellValue("C$lastRow", "TOTAL");
                $sheet = self::styleFooter($sheet, $config);
                break;

            case 'mileageReport':
                // Set general formulas
                foreach (['E'] as $totalLetterPosition) {
                    $sheet->setCellValue($totalLetterPosition . $lastRow, "=SUM($totalLetterPosition$starData:$totalLetterPosition$config->totalRows)");
                }

                $sheet->setCellValue("D$lastRow", "TOTAL");
                $sheet = self::styleFooter($sheet, $config);
                break;

            case 'offRoadReport':
                $startIndex = $config->startIndex + 1;
                $rows = range($config->startIndex + 1, $config->totalRows, 1);
                foreach ($rows as $row){
                    $cell = $sheet->getCell("H$row");
                    $cellLink = $cell->getValue();

                    $cell->getHyperlink()->setUrl($cellLink);
                    $cell->setValueExplicit(__('Chart'));
                }

                $sheet->cells("H$startIndex:H$config->totalRows", function ($cells) {
                    $cells->setValignment('center');
                    $cells->setAlignment('center');
                    $cells->setBackground('#0a0a15');
                    $cells->setFontColor(self::$fontColorInverse);
                    $cells->setFont(array(
                        'family' => 'Calibri',
                        'size' => '13',
                        'bold' => true,
                        'italic' => true,
                        'underline' => \PHPExcel_Style_Font::UNDERLINE_SINGLE
                    ));
                });
                break;

            case 'consolidatedRouteReport':
                $startIndex = $config->startIndex + 1;
                $rows = range($config->startIndex + 1, $config->totalRows, 1);
                foreach ($rows as $row){
                    $cell = $sheet->getCell("K$row");
                    $cellLink = $cell->getValue();

                    $cell->getHyperlink()->setUrl($cellLink);
                    $cell->setValueExplicit(__('Chart'));
                }

                $sheet->cells("K$startIndex:K$config->totalRows", function ($cells) {
                    $cells->setValignment('center');
                    $cells->setAlignment('center');
                    $cells->setBackground('#0a0a15');
                    $cells->setFontColor(self::$fontColorInverse);
                    $cells->setFont(array(
                        'family' => 'Calibri',
                        'size' => '13',
                        'bold' => true,
                        'italic' => true,
                        'underline' => \PHPExcel_Style_Font::UNDERLINE_SINGLE
                    ));
                });

                $sheet->cells("A$config->startIndex:A$config->totalRows", function ($cells) { $cells->setAlignment('center'); });
                $sheet->cells("B$config->startIndex:B$config->totalRows", function ($cells) { $cells->setAlignment('center'); });
                $sheet->cells("C$config->startIndex:C$config->totalRows", function ($cells) { $cells->setAlignment('center'); });

                $sheet->cells("G$config->startIndex:G$config->totalRows", function ($cells) { $cells->setAlignment('center'); });
                $sheet->cells("I$config->startIndex:I$config->totalRows", function ($cells) { $cells->setAlignment('center'); });

                break;

            case 'historicRouteReport':
                $sheet->cells("A$config->startIndex:E". $lastRow, function ($cells) {
                    $cells->setValignment('center');
                    $cells->setAlignment('center');
                });

                break;

            case 'reportMileageDateRange':
                foreach (['E'] as $totalLetterPosition) {
                    $sheet->setCellValue($totalLetterPosition . $lastRow, "=SUM($totalLetterPosition$starData:$totalLetterPosition$config->totalRows)");
                }

                $sheet->setColumnFormat(array(
                    "E$config->startIndex:E$lastRow" => "0.00"
                ));

                $sheet->setCellValue("D$lastRow", "TOTAL KM");
                $sheet = self::styleFooter($sheet, $config);
                break;
        }
        return $sheet;
    }

    public static function styleFooter($sheet, $config)
    {
        $lastRow = $config->totalRows + 1;
        /* STYLE FOR TOTAL ROW */
        $sheet->setHeight($lastRow, 25);
        //$sheet->mergeCells("A$lastRow:B$lastRow");
        $sheet->cells("A$lastRow:" . $config->lastLetter . $lastRow, function ($cells) {
            $cells->setValignment('center');
            $cells->setAlignment('center');
            $cells->setBackground('#0d4841');
            $cells->setFontColor(self::$fontColorInverse);
            $cells->setFont(array(
                'family' => self::$fontStyle,
                'size' => '12',
                'bold' => true
            ));
        });

        return $sheet;
    }
}