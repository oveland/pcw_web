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

    $query->chunk(50, function ($speedings) use (&$processed, $total) {
        foreach ($speedings as $speeding) {
            try {
                // getAddress(false, false):
                // 1er false: No refrescar si ya existe.
                // 2do false: No forzar (usa caché).
                // Al llamar a esto, si no tiene dirección, la busca y la guarda en address_locations.
                $speeding->getAddress(false, false);
            } catch (\Exception $e) {
                // Ignorar errores puntuales de conexión o datos
            }
            $processed++;
        }
        
        // Feedback visual simple
        echo "Procesando... $processed / $total completados.\r";
        
        // Pausa muy breve para dar respiro al CPU/API
        usleep(100000); // 0.1 segundos
    });

    $duration = round(microtime(true) - $startTime, 2);
    echo "\n\n¡Proceso terminado con éxito en $duration segundos!\n";
    echo "Ahora puedes ejecutar tu consulta SQL nuevamente.\n";

} catch (\Exception $e) {
    echo "\n\nERROR CRÍTICO: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
