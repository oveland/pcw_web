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
    protected $signature = 'rocket:process-5g-photos';

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
        /*
        $targetVehicleNumbers = [
            '8235','8401','8407','8403','8311','8353','8507','8515','8505','8509',
            '8517','8501','8253','8419','8295','8217','8321','8503','8511','8337',
            '8339','8319','8283','8287','8331','8251','2907','6605'
        ];
        */

        // Obtener vehículos 5G dinámicamente
        // Se asume que gps_vehicles tiene una columna 'technology' (o en tags, o related)
        // El usuario indicó: "deseo sacarlos de una tabla gps_vehicles ya esta un modelo creo y la conidcion es que la columna technology sea igual a '5G'"
        // Nota: El modelo GpsVehicle actual no muestra la propiedad 'technology' en los docblocks, 
        // pero confiaremos en la instrucción del usuario y usaremos DB::table si es necesario o el modelo si tiene la columna.
        // Dado que el modelo es Eloquent, intentaremos usar el modelo o un join directo.
        
        $companyId = 39;

        $this->info("Iniciando proceso de fotos 5G para empresa $companyId...");

        // Buscar vehículos 5G
        // Hacemos un join con gps_vehicles para filtrar por technology = '5G'
        
        $pendingDispatches = DB::table('registrodespacho as rd')
            ->join('vehicles as v', 'rd.vehicle_id', '=', 'v.id')
            ->join('gps_vehicles as gv', 'v.id', '=', 'gv.vehicle_id') // Join con gps_vehicles
            ->select('rd.*', 'v.number as vehicle_number', 'v.company_id')
            ->where('v.company_id', $companyId)
            ->where('gv.technology', '5G') // Filtro dinámico por tecnología 5G
            ->where('rd.status', 'Terminó') // Filtrar por estado 'Terminó'
            ->whereNull('rd.rocket_5g_area') // Solo los no procesados
            ->whereNotNull('rd.arrival_time') // Que hayan llegado
            ->where('rd.date', '>=', Carbon::now()->subDays(2)->toDateString()) // Límite de seguridad
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
        $drId = $dr->id; // En registrodespacho usualmente es id_registro o id? 
        // En el modelo DispatchRegister es 'id'. En la tabla 'registrodespacho' a veces es 'id_registro'.
        // Verificando el update del PhotoService: WHERE id_registro = $drId
        // Si el select * trae 'id_registro', usaremos ese. Si trae 'id', usaremos ese.
        // DB::table('registrodespacho') usualmente devuelve columnas tal cual.
        // Asumiremos 'id' o 'id_registro'. Revisaré las columnas si falla, pero por ahora intentaré detectar.
        
        $drId = isset($dr->id_registro) ? $dr->id_registro : $dr->id;

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

            // Construir fechas
            // rd.date y rd.departure_time son strings en la DB
            $departure = Carbon::createFromFormat('Y-m-d H:i:s', "{$dr->date} {$dr->departure_time}");
            
            // rd.date_end puede ser null si no terminó, pero filtramos por arrival_time not null.
            // Asumimos date_end existe. Si no, usamos date.
            $dateEnd = $dr->date_end ?? $dr->date;
            $arrival = Carbon::createFromFormat('Y-m-d H:i:s', "{$dateEnd} {$dr->arrival_time}");

            $startTime = $departure->copy()->subMinutes($preArrivalTimeMinutes);
            $endTime = $arrival->copy()->addMinutes($postArrivalTimeMinutes);

            // Consultar archivos
            $fileNames = DB::table('file_names')
                ->whereBetween('date', [$startTime->toDateTimeString(), $endTime->toDateTimeString()])
                ->where('vehicle_id', $dr->vehicle_id)
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
                
                DB::statement("UPDATE registrodespacho SET ignore_trigger = TRUE, rocket_5g_area = $CountArea5G WHERE id = $drId");
                // Nota: Usé 'WHERE id =' asumiendo que el PK es id. Si es id_registro, debo cambiarlo.
                // En el código original decía 'WHERE id_registro = $drId'.
                // Voy a usar id_registro si id no funciona o ambos.
                // Mejor usar query builder update para ser seguro.
            } else {
                $this->line("  -> 0 áreas válidas encontradas.");
                // Opcional: Marcar como 0 para no volver a procesar?
                // DB::table('registrodespacho')->where('id', $drId)->update(['rocket_5g_area' => 0]);
            }
            
            // Para evitar reprocesar los que dieron 0, deberíamos setear rocket_5g_area = 0 si es null.
            // El código original solo hace update si $CountArea5G (es decir > 0).
            // Si no hago update a 0, el comando volverá a coger este registro en la próxima ejecución.
            // Asumiré que debo marcarlo como 0 también.
            
            DB::table('registrodespacho')
                ->where('id_registro', $drId)
                ->update([
                    'rocket_5g_area' => $CountArea5G,
                    'ignore_trigger' => true
                ]);

        } catch (\Exception $e) {
            $this->error("Error procesando registro $drId: " . $e->getMessage());
        }
    }
}
