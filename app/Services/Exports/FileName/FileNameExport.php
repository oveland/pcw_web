<?php

namespace App\Services\Exports\FileName;

use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class FileNameExport
{
    public function export($files)
    {

        $fileName = 'nombres_archivos_' . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx';

        \Maatwebsite\Excel\Facades\Excel::create($fileName, function ($excel) use ($files) {
            $excel->sheet('Archivos', function ($sheet) use ($files) {
                // Cabecera
                $sheet->row(1, ['Nombre del Archivo']);

                // Datos
                $rowNumber = 2;
                foreach ($files as $file) {
                    $sheet->row($rowNumber++, [$file->file_name ?? '-']);
                }

                $sheet->setWidth(['A' => 60]);
            });
        })->export('xlsx');
    }
}
