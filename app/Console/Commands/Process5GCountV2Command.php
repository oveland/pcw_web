<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Routes\DispatchRegister;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class Process5GCountV2Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rocket:count-5g-v2';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía solicitudes a Runpod para conteo 5G V2 de registros terminados';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Iniciando proceso de conteo 5G V2...");

        $apiKey = config('services.runpod.api_key');
        $endpointId = config('services.runpod.endpoint_id');

        if (empty($apiKey) || empty($endpointId)) {
            $this->error("Error: Faltan credenciales de Runpod (RUNPOD_API_KEY, RUNPOD_ENDPOINT_ID) en el archivo .env");
            return;
        }

        // Buscar registros candidatos
        // Criterios:
        // 1. Vehículo con tecnología 5G (implícito en la relación o filtro)
        // 2. Status = 'Terminó'
        // 3. count_5g_v2 IS NULL (para no repetir)
        // 4. Recientes (últimos 5 días para seguridad, ajustable)
        
        $dispatches = DispatchRegister::query()
            ->select('registrodespacho.*', 'vehicles.number as vehicle_number')
            ->join('vehicles', 'registrodespacho.n_vehiculo', '=', 'vehicles.number')
            ->join('gps_vehicles', 'vehicles.id', '=', 'gps_vehicles.vehicle_id')
            ->where('gps_vehicles.technology', '5G')
            ->where('registrodespacho.status', DispatchRegister::COMPLETE) // 'Terminó'
            ->whereNull('registrodespacho.count_5g_v2')
            ->where('registrodespacho.fecha', '>=', Carbon::now()->subDays(5)->toDateString())
            ->get();

        $count = $dispatches->count();
        $this->info("Se encontraron $count registros pendientes para procesar.");

        if ($count === 0) {
            return;
        }

        $url = "https://api.runpod.io/v2/{$endpointId}/run";

        foreach ($dispatches as $dispatch) {
            $this->processDispatch($dispatch, $url, $apiKey);
        }

        $this->info("Proceso finalizado.");
    }

    private function processDispatch($dispatch, $url, $apiKey)
    {
        $id = $dispatch->id_registro ?? $dispatch->id; // Asegurar ID correcto
        $this->line("Procesando registro ID: $id - Vehículo: {$dispatch->vehicle_number}");

        try {
            // Payload para Runpod
            // Enviamos el id_registro para que el worker sepa qué actualizar
            $payload = [
                'input' => [
                    'id_registro' => $id,
                    'vehicle_number' => $dispatch->vehicle_number,
                    'date' => $dispatch->fecha, // O el campo de fecha correcto
                    'start_time' => $dispatch->h_reg_despachado,
                    'end_time' => $dispatch->h_reg_llegada,
                    // Agrega aquí más datos si el worker los necesita
                ]
            ];

            // Usamos Guzzle (cliente HTTP por defecto en Laravel) o curl
            // Laravel 7 usa Guzzle 6
            $client = new \GuzzleHttp\Client();
            
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => "Bearer {$apiKey}"
                ],
                'json' => $payload
            ]);

            $statusCode = $response->getStatusCode();
            $body = json_decode($response->getBody(), true);

            if ($statusCode >= 200 && $statusCode < 300) {
                $jobId = $body['id'] ?? 'N/A';
                $this->info("Solicitud enviada exitosamente. Job ID: $jobId");
            } else {
                $this->error("Error al enviar solicitud. Status: $statusCode");
            }

        } catch (\Exception $e) {
            $this->error("Excepción al llamar a Runpod para registro $id: " . $e->getMessage());
        }
    }
}
