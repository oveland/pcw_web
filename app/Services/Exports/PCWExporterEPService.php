<?php

namespace App\Services\Exports;

class PCWExporterEPService extends PCWExporterService
{

    public static function createSheet($excel, $dataExport, $disableFilters = false, $options = null)
    {
        $sheetTitle = isset($dataExport->sheetTitle) ? $dataExport->sheetTitle : $dataExport->subTitle;
        return $excel->sheet($sheetTitle, function ($sheet) use ($dataExport, $disableFilters, $options) {
            $startIndex = 3;
            $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '...'];

            $config = (object)[
                'type' => isset($dataExport->type) ? $dataExport->type : null,
                'startIndex' => $startIndex,
                'lastLetter' => $letters[count(array_keys($dataExport->data[0])) - 1],
                'totalRows' => count($dataExport->data) + $startIndex,
                'tariff'=>$dataExport->tariff,
                'nameRute'=>$dataExport->nameRute,
            ];
            $sheet->setCellValue('A1', "TRANSPORTES EXPRESO PALMIRA        ".$dataExport->title);
            $sheet->setCellValue('A2', $dataExport->subTitle." ".$config->nameRute);

            $sheet->fromArray($dataExport->data, null, 'A3', true, true);

            $sheet = self::sheetCustomReport($sheet, $config, $options);

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
            if (!$disableFilters) $sheet->setAutoFilter("A$startIndex:$config->lastLetter" . ($config->totalRows));

            /*  MAIN HEADER */
            $sheet->setHeight(1, 50);
            $sheet->mergeCells('A1:' . $config->lastLetter . '1');
            $sheet->cells('A1:' . $config->lastLetter . '1', function ($cells) {
                $cells->setValignment('center');
                $cells->setAlignment('center');
                //$cells->setBackground('#0e6d62');
                // $cells->setFontColor(self::$fontColorInverse);
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
                //$cells->setBackground('#0d4841');
                // $cells->setFontColor(self::$fontColorInverse);
                $cells->setFont(array(
                    'family' => self::$fontStyle,
                    'size' => '14',
                    'bold' => true
                ));
            });

            /* HEADER COLUMNS */
            $sheet->setHeight($startIndex, 40);
            $sheet->cells('A' . $startIndex . ':' . $config->lastLetter . $startIndex, function ($cells) {
                $cells->setValignment('center');
                $cells->setAlignment('center');
                //$cells->setBackground('#0d4841');
                // $cells->setFontColor(self::$fontColorInverse);
                $cells->setFont(array(
                    'family' => self::$fontStyle,
                    'size' => '14',
                    'bold' => true
                ));
            });
        });
    }

    public static function sheetCustomReport($sheet, $config, $options = null)
    {
        $lastRow = $config->totalRows + 1;
        $starData = $config->startIndex + 1;
        $position = $config->totalRows + $config->startIndex;
        $firma =$position + 10;
        $sheet->setSize('A' . $starData . ':' . $config->lastLetter . $lastRow, 10, 1000);
        $sheet->setSize('B' . $starData . ':' . $config->lastLetter . $lastRow, 25, 1000);
        $sheet->setSize('D' . $starData . ':' . $config->lastLetter . $lastRow, 20, 1000);
        $sheet->setSize('E' . $starData . ':' . $config->lastLetter . $lastRow, 20, 1000);
        $sheet->setSize('F' . $starData . ':' . $config->lastLetter . $lastRow, 20, 1000);
        $sheet->setSize('G' . $starData . ':' . $config->lastLetter . $lastRow, 20, 1000);
        $sheet->setSize('H' . $starData . ':' . $config->lastLetter . $lastRow, 15, 1000);
        $sheet->setSize('I' . $starData . ':' . $config->lastLetter . $lastRow, 25, 1000);
        $sheet->setSize('J' . $starData . ':' . $config->lastLetter . $lastRow, 20, 1000);
        $sheet->setSize('K' . $starData . ':' . $config->lastLetter . $lastRow, 20, 1000);
        $sheet->setSize('L' . $starData . ':' . $config->lastLetter . $lastRow, 20, 1000);
        $sheet->setSize('M' . $starData . ':' . $config->lastLetter . $lastRow, 20, 1000);
        $sheet->setSize('N' . $starData . ':' . $config->lastLetter . $lastRow, 20, 1000);

        //firma
        $f = 'I' . $firma . ":" . 'J' . $firma;
        $sheet->mergeCells($f, function ($cells) {
            $cells->setValignment('center');
            $cells->setAlignment('center');
            $cells->setFont(array(
                'family' => self::$fontStyle,
                'size' => '24',
                'bold' => true
            ));
        });

        $sheet->cells($f, function ($cells) {
            $cells->setValignment('center');
            $cells->setAlignment('center');
            $cells->setBorder('thin');
            $cells->setFont(array(
                'family' => self::$fontStyle,
                'size' => '24',
                //'bold' => true
            ));
        });
        $sheet->setCellValue("I$firma", "FIRMA");

        $totals = 'G' . $position . ":" . 'O' . $position;
        $sheet->cells($totals, function ($cells) {
            $cells->setValignment('center');
            $cells->setAlignment('center');
            $cells->setFont(array(
                'family' => self::$fontStyle,
                'size' => '24',
                'bold' => true
            ));
        });
        $sheet->setCellValue("B$position", "TOTAL VUELOS=");
        $sheet->setCellValue("C$position", "=C$config->totalRows");
        $sheet->cells('C' . $position,function ($cells) {
            $cells->setValignment('left');
            $cells->setAlignment('left');
            $cells->setFont(array(
                'family' => self::$fontStyle,
                'size' => '24',
                'bold' => true
            ));
        });
        $sheet->cells('B' . $position, function ($cells) {
            $cells->setValignment('right');
            $cells->setAlignment('right');
            $cells->setFont(array(
                'family' => self::$fontStyle,
                'size' => '24',
                'bold' => true
            ));
        });

        $sheet->setColumnFormat(array(
            'I4:I39' => '$#,##0_-'
        ));
        $sheet->setCellValue("K$position", "=SUM(K$starData:K$lastRow)");

        $sheet->cells('A' . $starData . ':' . $config->lastLetter . $lastRow, function ($cells) {
            $cells->setValignment('center');
            $cells->setAlignment('center');
            //$cells->setBackground('#0d4841');
            // $cells->setFontColor(self::$fontColorInverse);
            $cells->setFont(array(
                'family' => self::$fontStyle,
                'size' => '18',
                //'bold' => true
            ));
        });
        switch ($config->type) {
            case 'passengerReportTotalFooter':
                // Set general formulas
                foreach (['F', 'G', 'H', 'I'] as $totalLetterPosition) {
                    $sheet->setCellValue($totalLetterPosition . $lastRow, "=SUM($totalLetterPosition$starData:$totalLetterPosition$config->totalRows)");
                }

                $sheet->setCellValue("A$lastRow", "TOTAL");
                $sheet = self::styleFooter($sheet, $config);

                $sheet->cells("A$config->startIndex:C$config->totalRows", function ($cells) {
                    $cells->setAlignment('center');
                });
                $sheet->cells("E$config->startIndex:H$config->totalRows", function ($cells) {
                    $cells->setAlignment('center');
                });
                break;

            case 'passengerReportByRangeTotalFooter':
                foreach (['H', 'I', 'J', 'K', 'L', 'M', 'N', 'O'] as $totalLetterPosition) {
                    $sheet->setCellValue($totalLetterPosition . $lastRow, "=SUM($totalLetterPosition$starData:$totalLetterPosition$config->totalRows)");
                }

                $sheet->setCellValue("A$lastRow", "TOTAL");
                $sheet->setCellValue("F$lastRow", $options['totalVehicles']);
                $sheet->setCellValue("G$lastRow", $options['totalDates']);

                for ($i = $starData; $i <= $lastRow; $i++) {
                    $sheet->setCellValue("O$i", "=IF(H$i, L$i/I$i, 0)");
                }

                $sheet->setColumnFormat(array(
                    "O$config->startIndex:O$lastRow" => "0.00"
                ));

                $sheet = self::styleFooter($sheet, $config);
                break;

            case 'routeReportByVehicle':
                // Set general formulas
                /*for ($i = $starData; $i < $lastRow; $i++) {
                    $sheet->setCellValue("M$i", "=L$i-K$i");
                    $sheet->setCellValue("N$i", "=M$i+" . (($i > $starData) ? ("N" . ($i - 1)) : "0"));
                }*/
                for ($i = $starData; $i < $lastRow; $i++) {
                    $sheet->setCellValue("I$i", "=H$i*".intval($config->tariff));
                }

                $sheet->setCellValue("G$position", "TOTALES");
                $sheet->setCellValue("H$position", "=SUM(H$starData:H$lastRow)");
                $sheet->setCellValue("I$position", "=SUM(I$starData:I$lastRow)");
                $sheet->setCellValue("L$position", "=SUM(L$starData:L$lastRow)");
                $sheet->setCellValue("N$position", "=SUM(N$starData:N$lastRow)");
                $sheet->setCellValue("O$position", "=SUM(O$starData:O$lastRow)");
                /* $diference=$lastRow-$starData;

                 for ($var = $starData; $var <= $diference; $var++) {
                         $r=$starData+$var;
                     $sheet->setCellValue("C$r","$var"); }*/

                break;

            case 'routeReportUngrouped':
                $sheet->cells("A$config->startIndex:" . $config->lastLetter . $lastRow, function ($cells) {
                    $cells->setValignment('center');
                    $cells->setAlignment('center');
                });
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

            case 'controlPointsReport':
                $startIndex = $config->startIndex + 1;
                $rows = range($config->startIndex + 1, $config->totalRows, 1);

                foreach ($rows as $row) {
                    $cell = $sheet->getCell($config->lastLetter . $row);
                    $cellLink = $cell->getValue();

                    $cell->getHyperlink()->setUrl($cellLink);
                    $cell->setValueExplicit(__('Chart'));
                }

                $sheet->cells("$config->lastLetter$startIndex:$config->lastLetter$config->totalRows", function ($cells) {
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

            case 'offRoadReport':
                $startIndex = $config->startIndex + 1;
                $rows = range($config->startIndex + 1, $config->totalRows, 1);
                foreach ($rows as $row) {
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
                $linkColumn = $options['linkColumn'];
                foreach ($rows as $row) {
                    $cell = $sheet->getCell("$linkColumn$row");
                    $cellLink = $cell->getValue();

                    $cell->getHyperlink()->setUrl($cellLink);
                    $cell->setValueExplicit(__('Chart'));
                }

                $sheet->cells("$linkColumn$startIndex:$linkColumn$config->totalRows", function ($cells) {
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

                $sheet->cells("A$config->startIndex:A$config->totalRows", function ($cells) {
                    $cells->setAlignment('center');
                });
                $sheet->cells("B$config->startIndex:B$config->totalRows", function ($cells) {
                    $cells->setAlignment('center');
                });
                $sheet->cells("C$config->startIndex:C$config->totalRows", function ($cells) {
                    $cells->setAlignment('center');
                });

                $sheet->cells("G$config->startIndex:G$config->totalRows", function ($cells) {
                    $cells->setAlignment('center');
                });
                $sheet->cells("I$config->startIndex:I$config->totalRows", function ($cells) {
                    $cells->setAlignment('center');
                });

                break;

            case 'managementReport':
                $startIndex = $config->startIndex + 1;
                $rows = range($config->startIndex + 1, $config->totalRows, 1);

                $sheet->cells("A$config->startIndex:A$config->totalRows", function ($cells) {
                    $cells->setValignment('center');
                    $cells->setAlignment('center');
                });

                $sheet->cells("C$config->startIndex:J$config->totalRows", function ($cells) {
                    $cells->setValignment('center');
                    $cells->setAlignment('center');
                });

                break;

            case 'currentVehicleStatusReport':
                $startIndex = $config->startIndex + 1;
                $rows = range($config->startIndex + 1, $config->totalRows, 1);

                $sheet->cells("A$config->startIndex:H$config->totalRows", function ($cells) {
                    $cells->setValignment('center');
                    $cells->setAlignment('center');
                });

                $sheet->setColumnFormat(array(
                    "G$config->startIndex:H$lastRow" => "0.00"
                ));

                break;

            case 'historicRouteReport':
                $sheet->cells("A$config->startIndex:E" . $lastRow, function ($cells) {
                    $cells->setValignment('center');
                    $cells->setAlignment('center');
                });

                break;

            case 'reportMileageDateRange':
                foreach (['F'] as $totalLetterPosition) {
                    $sheet->setCellValue($totalLetterPosition . $lastRow, "=SUM($totalLetterPosition$starData:$totalLetterPosition$config->totalRows)");
                }

                $sheet->setColumnFormat(array(
                    "F$config->startIndex:F$lastRow" => "0.00"
                ));

                $sheet->setCellValue("E$lastRow", "TOTAL KM");
                $sheet = self::styleFooter($sheet, $config);
                break;

            case 'consolidatedRouteVehicle':
                $sheet->cells("A$config->startIndex:A" . $lastRow, function ($cells) {
                    $cells->setValignment('center');
                    $cells->setAlignment('center');
                });

                $sheet->cells("C$config->startIndex:F" . $lastRow, function ($cells) {
                    $cells->setValignment('center');
                    $cells->setAlignment('center');
                });
                break;
        }
        return $sheet;
    }
}