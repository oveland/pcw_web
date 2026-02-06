<?php

// Cargar el entorno de Laravel manualmente
require __DIR__ . '/bootstrap/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Vehicles\Speeding;

// Configuración de fechas
$fromDate = '2026-01-01';
$toDate = '2026-01-31';

echo "--------------------------------------------------------\n";
echo "Iniciando geocodificación manual (Script Standalone)\n";
echo "Rango: $fromDate al $toDate\n";
echo "--------------------------------------------------------\n";

try {
    // Buscar registros
    $query = Speeding::whereBetween('date', ["$fromDate 00:00:00", "$toDate 23:59:59"])
        ->where('speed', '>', 0);

    $total = $query->count();
    echo "Se encontraron $total registros de excesos de velocidad.\n";

    if ($total === 0) {
        echo "No hay nada que procesar. Verifica las fechas.\n";
        exit;
    }

    $processed = 0;
    $startTime = microtime(true);

    // OPTIMIZACIÓN: Solo procesar los que NO tienen dirección aún
    // Esto requiere un LEFT JOIN o whereDoesntHave, pero para ser seguros en legacy,
    // iteramos y AddressLocation lo chequeará rápido.
    
    // Chunk size aumentado para mejor throughput si la mayoría ya existe
    $query->chunk(200, function ($speedings) use (&$processed, $total) {
        foreach ($speedings as $speeding) {
            try {
                // getAddress(false, false)
                // En el código original de Location.php:
                // if ($refresh || !$addressLocation || !$addressLocation->address) { ... }
                // Así que solo llamará a la API si no tiene dirección en la BD.
                // IMPORTANTE: Hemos modificado Geolocation.php para que NO devuelva "" si force=false.
                $speeding->getAddress(false, false);
            } catch (\Exception $e) {
                // Ignorar errores puntuales
            }
            $processed++;
        }
        
        echo "Procesando... $processed / $total completados.\r";
        // usleep eliminado para máxima velocidad, el cuello de botella será la API externa
    });

    $duration = round(microtime(true) - $startTime, 2);
    echo "\n\n¡Proceso terminado con éxito en $duration segundos!\n";

} catch (\Exception $e) {
    echo "\n\nERROR CRÍTICO: " . $e->getMessage() . "\n";
}
