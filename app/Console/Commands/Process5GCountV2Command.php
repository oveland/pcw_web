<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
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
    protected $description = 'Envía solicitudes a servidor privado para conteo 5G V2 de registros terminados';

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

        // Buscar registros candidatos
        // Criterios:
        // 1. Vehículo con tecnología 5G (implícito en la relación o filtro)
        // 2. Status = 'Terminó'
        // 3. count_5g_v2 IS NULL (para no repetir)
        // 4. Recientes (últimos 5 días para seguridad, ajustable)
        
        $dispatches = DispatchRegister::query()
            ->select('dispatch_registers.*', 'vehicles.number as vehicle_number', 'vehicles.id as real_vehicle_id')
            ->join('vehicles', 'dispatch_registers.vehicle_id', '=', 'vehicles.id')
            ->join('gps_vehicles', 'vehicles.id', '=', 'gps_vehicles.vehicle_id')
            ->where('gps_vehicles.technology', '5G')
            ->where('dispatch_registers.status', DispatchRegister::COMPLETE) // 'Terminó'
            ->whereNull('dispatch_registers.count_5g_v2')
            ->where('dispatch_registers.date', '>=', Carbon::now()->subDays(5)->toDateString())
            ->get();

        $count = $dispatches->count();
        $this->info("Se encontraron $count registros pendientes para procesar.");

        if ($count === 0) {
            return;
        }

        $url = "http://172.16.22.8:8080/process";

        foreach ($dispatches as $dispatch) {
            $this->processDispatch($dispatch, $url);
        }

        $this->info("Proceso finalizado.");
    }

    private function processDispatch($dispatch, $url)
    {
        $id = $dispatch->id; // Asegurar ID correcto
        $this->line("Procesando registro ID: $id - Vehículo: {$dispatch->vehicle_number}");

        try {
            $minutesToSubtract = 15;

            $dateStr = explode(' ', $dispatch->date)[0];
            $timeStr = explode('.', $dispatch->departure_time)[0];
            
            $this->line("Debug Dates - Date: '$dateStr', Time: '$timeStr'");
            
            if (empty($dateStr) || empty($timeStr)) {
                $this->error("Fecha o hora de despacho inválida para registro $id");
                return;
            }

            try {
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStr)) {
                     $startDateTime = Carbon::createFromFormat('Y-m-d H:i:s', "$dateStr $timeStr");
                } 
                elseif (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $dateStr)) {
                     $startDateTime = Carbon::createFromFormat('d/m/Y H:i:s', "$dateStr $timeStr");
                }
                else {
                    $startDateTime = Carbon::parse("$dateStr $timeStr");
                }
            } catch (\Exception $e) {
                 $this->error("Error parseando fecha inicio: " . $e->getMessage());
                 try {
                    $startDateTime = Carbon::parse($dispatch->date . ' ' . $dispatch->departure_time);
                 } catch (\Exception $e2) {
                    $this->error("Fallo total en fecha para registro $id");
                    return;
                 }
            }
            
            $startDateTime->subMinutes($minutesToSubtract);

            $endTimeStr = explode('.', $dispatch->arrival_time)[0];
            
            if (!empty($dispatch->date_end)) {
                $endDateStr = explode(' ', $dispatch->date_end)[0];
                if (strpos($dispatch->date_end, ':') !== false) {
                    $endDateTime = Carbon::parse($dispatch->date_end);
                } else {
                    $endDateTime = Carbon::parse("$endDateStr $endTimeStr");
                }
            } else {
                $originalStart = $startDateTime->copy()->addMinutes($minutesToSubtract);
                $endDateTime = Carbon::parse("$dateStr $endTimeStr");
                
                if ($endDateTime->lt($originalStart)) {
                    $endDateTime->addDay();
                }
            }

            $payload = [
                'vehicle_id' => $dispatch->real_vehicle_id,
                'start' => $startDateTime->toDateTimeString(),
                'end' => $endDateTime->toDateTimeString(),
                'id_registro' => $id
            ];

            $client = new \GuzzleHttp\Client();
            
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json'
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
            $this->error("Excepción al llamar al servidor privado para registro $id: " . $e->getMessage());
        }
    }
}
