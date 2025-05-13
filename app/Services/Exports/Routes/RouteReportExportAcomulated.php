<?php

namespace App\Services\Exports\Routes;

// No necesitas los 'use Maatwebsite\Excel\Concerns...' para la v2.1
// Tampoco necesitas 'use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;'
// ni las constantes de estilo de PhpSpreadsheet directamente aquí,
// ya que los estilos se aplicarán en el controlador.

class RouteReportExportAcomulated
{
    protected $data;
    protected $headings = [];

    public function __construct(array $data)
    {
        // Los datos ya vienen formateados con claves desde tu getRouteDispatchReport
        // Ejemplo: [['Vehículo' => 'ABC-123', 'Cantidad de Despachos' => 10]]
        $this->data = $data;


        if (empty($this->data)) {
            // Si no hay datos, usamos cabeceras por defecto
            // y preparamos una fila vacía para que las cabeceras se muestren
            $this->headings = ['Vehículo', 'Cantidad de Vuelos'];
            // Necesitamos una fila con el número correcto de columnas para que se generen
            $this->data = [array_fill_keys($this->headings, '')];
        } else {
            // Obtenemos las cabeceras de la primera fila de datos reales
            $this->headings = array_keys($this->data[0]);
        }


    }

    /**
     * Devuelve los datos como un array de arrays (solo valores).
     * @return array
     */
    public function getExportData(): array
    {

        $rows = [];
        // Si los datos originales estaban vacíos, $this->data contendrá una fila con valores vacíos.
        // Si no queremos esa fila vacía en la exportación (solo las cabeceras),
        // podríamos necesitar una lógica adicional aquí o en el constructor.
        // Por ahora, la incluiremos para que las columnas se generen si no hay datos.
        foreach ($this->data as $row) {
            $rows[] = array_values($row);
        }
        return $rows;
    }

    /**
     * Devuelve las cabeceras.
     * @return array
     */
    public function getHeadings(): array
    {
        return $this->headings;
    }

    /**
     * Devuelve el título para la hoja.
     * @return string
     */
    public function getTitle(): string
    {
        return 'Reporte de Despachos';
    }

    // El método styles() de la v3 ya no se usa aquí.
    // La lógica de estilos se aplicará en el controlador.
    // El método shouldAutoSize() tampoco, se manejará en el controlador.
}