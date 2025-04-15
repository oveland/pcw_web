<?php

namespace App\Services\Exports\Drivers;

use Maatwebsite\Excel\Facades\Excel;
use App\Services\Exports\PCWExporterService;
use Carbon\Carbon;

use App\Models\Drivers\Driver;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithTitle;

class DriverExportService
{
    /**
     * @param $driverReport
     * @param bool $store
     * @return string
     */
    public function exportDriverReport($driverReport, $store = false)
    {
        $now = Carbon::now()->format('Y-m-d_H-i-s');
        $fileName = 'Driver_Report_' . $now;

        // Procesar los datos
        $dataExcel = [];

        foreach ($driverReport as $driverCode => $reportData) {
            $driver = Driver::where('code', $driverCode)->first();

            if ($driver) {
                foreach ($reportData->dispatchRegisters as $dispatchRegister) {
                    $dataExcel[] = [
                        '#'                  => count($dataExcel) + 1,
                        'Nombre Completo'    => $driver->fullName(),
                        'Cédula'             => $driver->identity,
                        'fecha'                => $reportData->dateReport,
                        'Hora despacho'       => $dispatchRegister->departure_time,
                        'Hora llegada'        => $dispatchRegister->arrival_time,
                        'Estado de viaje '    => $dispatchRegister->status,
                        'codigo Cond.'        => $dispatchRegister->driver_code,
                    ];
                }
            }
        }

        if (empty($dataExcel)) {
            return 'No hay datos para exportar.';
        }

        // Generar el archivo Excel
        $excelFile = Excel::create($fileName, function ($excel) use ($dataExcel) {
            $excel->sheet('Driver Report', function ($sheet) use ($dataExcel) {
                // Agregar encabezados
                $sheet->fromArray(
                    array_merge([array_keys($dataExcel[0])], $dataExcel), // Encabezado + datos
                    null,
                    'A1',
                    false, // No reemplazar valores nulos
                    false  // No incluir encabezados automáticamente
                );

                // Aplicar estilos opcionales
                $sheet->row(1, function ($row) {
                    $row->setFontWeight('bold');
                });
            });
        });

        if ($store) {
            $excelFile->store('xlsx', storage_path('app/public'));
            return storage_path("app/public/{$fileName}.xlsx");
        }

        return $excelFile->export('xlsx');
    }

}
