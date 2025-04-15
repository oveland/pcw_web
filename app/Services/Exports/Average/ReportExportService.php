<?php

namespace App\Services\Exports\Average;

use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ReportExportService
{

    public function export($data)
    {
        // Nombre del archivo
        $fileName = 'reporte_pasajeros_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx';

        // Generar el archivo Excel
        Excel::create($fileName, function ($excel) use ($data) {
            foreach ($data as $type => $rows) {
                $excel->sheet($type, function ($sheet) use ($type, $rows) {
                    // Título principal
                    $sheet->mergeCells('A1:D1');
                    $sheet->setCellValue('A1', "Promedio de Pasajeros - $type");

                    // Encabezados de columna
                    $headers = ['Fecha', 'Rango de Hora', 'Promedio de Pasajeros'];
                    $sheet->fromArray($headers, null, 'A2');

                    // Datos
                    $rowIndex = 3; // Comenzar desde la fila 3
                    foreach ($rows as $row) {
                        $sheet->setCellValue('A' . $rowIndex, $row['fecha']);
                        $sheet->setCellValue('B' . $rowIndex, $row['intervalo']);
                        $sheet->setCellValue('C' . $rowIndex, $row['promedio_pasajeros'] ?? '');
                        $rowIndex++;
                    }
                    $sheet->setWidth([
                        'A' => 20,
                        'B' => 20,
                        'C' => 15, 
                    ]);
                });
            }
        })->export('xlsx');
    }
}