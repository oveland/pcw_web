<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Routes\DispatchRegister;

class Process5GPhotosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rocket:process-5g-photos {--company=39}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Procesa fotos 5G para vehículos específicos de la empresa 39';

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
    
        $companyId = 39;

        $this->info("Iniciando proceso de fotos 5G para empresa $companyId...");

        // Buscar vehículos 5G        
        $pendingDispatches = DB::table('registrodespacho as rd')
            ->join('vehicles as v', 'rd.n_vehiculo', '=', 'v.number') // Join usando el número del vehículo y la compañía
            ->join('gps_vehicles as gv', 'v.id', '=', 'gv.vehicle_id') // Join con gps_vehicles
            ->select('rd.*', 'v.number as vehicle_number', 'v.company_id', 'v.id as real_vehicle_id') // Obtenemos el ID real del vehículo
            ->where('v.company_id', $companyId)
            ->where('rd.id_empresa', $companyId) // Aseguramos que el despacho sea de la misma empresa
            ->where('gv.technology', '5G') // Filtro dinámico por tecnología 5G
            ->whereNotNull('rd.h_reg_llegada') 
            ->where(function($q) {
                $q->whereNull('rd.cancelado')->orWhere('rd.cancelado', 0)->orWhere('rd.cancelado', false);
            })
            ->whereNull('rd.rocket_5g_area')
            ->where('rd.fecha', '>=', Carbon::now()->subDays(2)->toDateString()) // date -> fecha
            ->get();


        $count = $pendingDispatches->count();
        $this->info("Se encontraron $count registros pendientes.");

        foreach ($pendingDispatches as $dr) {
            $this->processDispatch($dr);
        }

        $this->info("Proceso finalizado.");
    }

    private function processDispatch($dr)
    {
        $drId = $dr->id_registro; // Usando columna correcta id_registro
        
        $this->line("Procesando registro ID: $drId - Vehículo: $dr->vehicle_number");

        try {
            // Parámetros configurables
            $preArrivalTimeMinutes = 10;
            $postArrivalTimeMinutes = 0;
            
            if ($dr->vehicle_number == '2907'){
                $minFilesPerArea = 2;
            } else {
                $minFilesPerArea = 15;
            }

            // Log raw dates for debugging
            $this->line("Raw dates - Fecha: '{$dr->fecha}', Despacho: '{$dr->h_reg_despachado}', Llegada: '{$dr->h_reg_llegada}', DateEnd: '{$dr->date_end}'");

            // Limpiamos los datos antes de parsear para evitar "Unexpected data"
            $dateStr = explode(' ', $dr->fecha)[0]; // Tomar solo la fecha
            $timeStr = explode('.', $dr->h_reg_despachado)[0]; // Quitar milisegundos si existen
            
            if (empty($dateStr) || empty($timeStr)) {
                $this->error("Fecha o hora de despacho inválida/vacía para registro $drId");
                return;
            }

            try {
                // Intentar primero con Y-m-d
                if (strpos($dateStr, '-') !== false) {
                    $departure = Carbon::createFromFormat('Y-m-d H:i:s', "$dateStr $timeStr");
                } else {
                    // Si no tiene guiones, asumir d/m/Y (formato detectado en logs: 27/12/2025)
                    $departure = Carbon::createFromFormat('d/m/Y H:i:s', "$dateStr $timeStr");
                }
            } catch (\Exception $e) {
                $this->error("Error parseando salida: '$dateStr $timeStr' - " . $e->getMessage());
                return;
            }
            
            $dateEnd = $dr->date_end ?? $dr->fecha;
            $dateEndStr = explode(' ', $dateEnd)[0];
            $timeEndStr = explode('.', $dr->h_reg_llegada)[0];

            if (empty($dateEndStr) || empty($timeEndStr)) {
                $this->error("Fecha o hora de llegada inválida/vacía para registro $drId");
                return;
            }

            try {
                if (strpos($dateEndStr, '-') !== false) {
                    $arrival = Carbon::createFromFormat('Y-m-d H:i:s', "$dateEndStr $timeEndStr");
                } else {
                    $arrival = Carbon::createFromFormat('d/m/Y H:i:s', "$dateEndStr $timeEndStr");
                }
            } catch (\Exception $e) {
                $this->error("Error parseando llegada: '$dateEndStr $timeEndStr' - " . $e->getMessage());
                return;
            }

            $startTime = $departure->copy()->subMinutes($preArrivalTimeMinutes);
            $endTime = $arrival->copy()->addMinutes($postArrivalTimeMinutes);

            // Consultar archivos
            $fileNames = DB::table('file_names')
                ->whereBetween('date', [$startTime->toDateTimeString(), $endTime->toDateTimeString()])
                ->where('vehicle_id', $dr->real_vehicle_id)
                ->pluck('file_name'); // pluck devuelve una Collection en Laravel reciente o array en viejos. DB builder ->pluck devuelve Collection.

            // Grouping logic
            $groupedFiles = $fileNames->filter(function ($fileName) {
                $pattern = '/^.*ch[0-9]+_(\d+)(?:_id\d+)?_(\d{14})_[TE]\.jpg$/';
                if (preg_match($pattern, $fileName, $matches)) {
                    return true;
                }
                return false;
            })->groupBy(function ($fileName) {
                preg_match('/^.*ch[0-9]+_(\d+)(?:_id\d+)?_\d{14}_[TE]\.jpg$/', $fileName, $matches);
                return $matches[1];
            });

            // Filtrar grupos
            $filteredGroups = $groupedFiles->filter(function ($group) use ($minFilesPerArea) {
                return $group->count() >= $minFilesPerArea;
            });

            $CountArea5G = $filteredGroups->count();

            if ($CountArea5G > 0) {
                $this->info("  -> Encontradas $CountArea5G áreas válidas. Actualizando...");
                
                // Usando query builder que es más seguro y consistente
                DB::table('registrodespacho')
                    ->where('id_registro', $drId)
                    ->update([
                        'rocket_5g_area' => $CountArea5G,
                        'ignore_trigger' => true
                    ]);
              
            } else {
                $this->line("  -> 0 áreas válidas encontradas.");
                // Opcional: Marcar como 0 para no volver a procesar?
                // DB::table('registrodespacho')->where('id_registro', $drId)->update(['rocket_5g_area' => 0]);
            }
            
            // ELIMINADO bloque duplicado que causaba error porque intentaba actualizar siempre al final
            // y el update anterior ya cubría el caso > 0.
            // Si queremos actualizar siempre (incluso si es 0), descomentar la linea en el else.

        } catch (\Exception $e) {
            $this->error("Error procesando registro $drId: " . $e->getMessage());
        }
    }
}
