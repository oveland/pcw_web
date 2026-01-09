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

        // Si no hay endpointId configurado, usar el proporcionado por defecto
        if (empty($endpointId)) {
            $endpointId = '5djqi13hv4sxwp';
        }

        if (empty($apiKey)) {
            $this->error("Error: Falta RUNPOD_API_KEY en el archivo .env. Por favor genérela en la consola de Runpod.");
            return;
        }

        // Buscar registros candidatos
        // Criterios:
        // 1. Vehículo con tecnología 5G (implícito en la relación o filtro)
        // 2. Status = 'Terminó'
        // 3. count_5g_v2 IS NULL (para no repetir)
        // 4. Recientes (últimos 5 días para seguridad, ajustable)
        
        $dispatches = DispatchRegister::query()
            ->select('registrodespacho.*', 'vehicles.number as vehicle_number', 'vehicles.id as real_vehicle_id')
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

        // Usar api.runpod.ai como solicitó el usuario
        $url = "https://api.runpod.ai/v2/{$endpointId}/run";

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
            // Lógica de fechas
            // Restar 15 minutos a la hora de despacho
            $minutesToSubtract = 15;

            // Construir timestamps completos
            // Fecha base
            $dateStr = explode(' ', $dispatch->fecha)[0];
            
            // Hora despacho
            $timeStr = explode('.', $dispatch->h_reg_despachado)[0];
            if (empty($dateStr) || empty($timeStr)) {
                $this->error("Fecha o hora de despacho inválida para registro $id");
                return;
            }

            try {
                if (strpos($dateStr, '-') !== false) {
                    $startDateTime = Carbon::createFromFormat('Y-m-d H:i:s', "$dateStr $timeStr");
                } else {
                    $startDateTime = Carbon::createFromFormat('d/m/Y H:i:s', "$dateStr $timeStr");
                }
            } catch (\Exception $e) {
                 // Fallback si falla el formato exacto, intentar parsear flexiblemente
                 $startDateTime = Carbon::parse("$dateStr $timeStr");
            }
            
            // Restar los minutos configurados
            $startDateTime->subMinutes($minutesToSubtract);

            // Hora llegada (End)
            // Si hay date_end, usarlo, si no, calcular con fecha base (cuidado con cambio de día)
            // Asumiremos que h_reg_llegada es correcto. Si date_end existe, mejor.
            $endTimeStr = explode('.', $dispatch->h_reg_llegada)[0];
            
            if (!empty($dispatch->date_end)) {
                $endDateStr = explode(' ', $dispatch->date_end)[0]; // Si date_end es datetime
                // Si date_end solo es fecha, usar esa fecha con endTimeStr
                // Si date_end es Y-m-d
                if (strpos($dispatch->date_end, ':') !== false) {
                    // Es datetime completo
                    $endDateTime = Carbon::parse($dispatch->date_end);
                } else {
                    $endDateTime = Carbon::parse("$endDateStr $endTimeStr");
                }
            } else {
                // Si no hay date_end, inferir. Si la hora de llegada es menor a la de salida, es el día siguiente
                // Pero startDateTime ya fue restado, así que comparar con original
                $originalStart = $startDateTime->copy()->addMinutes($minutesToSubtract);
                $endDateTime = Carbon::parse("$dateStr $endTimeStr");
                
                if ($endDateTime->lt($originalStart)) {
                    $endDateTime->addDay();
                }
            }

            // Payload para Runpod
            $payload = [
                'input' => [
                    'vehicle_id' => $dispatch->real_vehicle_id, // ID numérico del vehículo (ej: 2685)
                    'start' => $startDateTime->toDateTimeString(), // "2026-01-05 21:05:00"
                    'end' => $endDateTime->toDateTimeString(),     // "2026-01-06 03:59:21"
                    'id_registro' => $id
                ]
            ];

            // Usamos Guzzle (cliente HTTP por defecto en Laravel)
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
