<?php

namespace App\Console\Commands\DB;

use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class FreeSizeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:free-size {--from=} {--to=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Release DB data from locations, reports and vehicle_status_reports tables';

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
     * Devuelve los parámetros de conifuguración del mantenimiento en una o varias tablas
     *
     * restore
     * release
     * hasBackup
     *
     * @return Collection
     */
    public function getMaintenanceData()
    {
        $from = $this->option('from');
        $to = $this->option('to');

        $tables = collect([
            'app_photos' => [
                'restore' => false,
            ],
            'vehicle_status_reports' => [
                'restore' => false,
            ],
            'locations' => [
                'restore' => false,
            ],
            'reports' => [
                'restore' => false,
            ],
        ]);

        return collect([
            [
                'from' => '2023-08-27',
                'to' => '2023-08-27',
                'tables' => $tables
            ],
        ]);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $initialDate = Carbon::now();
        $this->log('Executing db maintenance at ' . $initialDate->toDateTimeString());

        $maintenanceData = $this->getMaintenanceData()->sortByDesc('from');

        $maintenanceData->each(function ($maintenance) {
            $maintenance = json_decode(json_encode($maintenance), FALSE); // To Object
            $this->process($maintenance);
        });

//        $query = "INSERT INTO locations (id, version, date, date_created, dispatch_register_id, distance, last_updated, latitude, longitude, odometer,orientation, speed, status, vehicle_id, off_road, vehicle_status_id, speeding, current_mileage, ard_off_road) SELECT * FROM locations_0 WHERE date < current_date";
//        $this->log($query);
//        DB::statement($query);

        $this->log("Maintenance finished at " . Carbon::now()->toDateTimeString() . " | From " . Carbon::now()->diffForHumans($initialDate));

    }

    /**
     * Función principal para procesar el backup de una tablas
     *
     * @param $maintenance
     * @return void
     */
    public function process($maintenance)
    {
        $tables = $maintenance->tables;

        if ($maintenance->from && $maintenance->to && $maintenance->from <= $maintenance->to) {
            foreach ($tables as $table => $options) {
                $this->processTable($table, $maintenance->from, $maintenance->to, $options);
            }
        } else {
            $this->log("Invalid date range From: $maintenance->from and To: $maintenance->to");
        }
    }

    /**
     * Procesa el backup de una tabla creando otra tabla con los datos dentro un rango de fecha
     * Ej:  Ej locations -> locations_2030_01_27
     *
     * También es posible restaurar los datos de una tabla backup a la tabla original (Cuando $restore = true)
     *
     * @param $table
     * @param $from
     * @param $to
     * @param $options
     * @return void
     */
    public function processTable($table, $from, $to, $options)
    {
        $release = $options->release ?? false;
        $restore = $options->restore ?? false;

        $this->log("   Processing table: $table...");
        $tableBackup = $this->getTableBackup($table, $to);

        if ($restore) {
            $this->restore($table, $from, $to);
        } else {
            DB::statement("DROP TABLE IF EXISTS $tableBackup");
            $query = "CREATE TABLE $tableBackup AS SELECT * FROM $table WHERE date BETWEEN '$from 00:00:00' AND '$to 23:59:59'";
            $this->log("       - $query");
            DB::statement($query);

            $query = "TRUNCATE TABLE $table";
            $this->log("       - $query");
            DB::statement($query);

            $this->restore($table, $from, $to);

//            $backupSuccess = $this->processBackup($table, $from, $to);
//            if ($release && $backupSuccess) {
//                $this->releaseTable($table, $from, $to);
//            }
        }
    }

    /**
     * Reestablece los datos de una tabla backup a la tabla original
     * Ej locations_2030_01_27 -> locations
     *
     * @param $table
     * @param $from
     * @param $to
     * @return void
     */
    private function restore($table, $from, $to)
    {
        $tableBackup = $this->getTableBackup($table, $to);
        $tableColumns = $this->getTableColumns($table);
        $initialDate = Carbon::now();

        $this->log("       ******** RESTORING TABLE $table FROM TABLE $tableBackup at " . $initialDate->toDateTimeString());

        $query = "INSERT INTO $table $tableColumns SELECT * FROM $tableBackup";
        $this->log("       - $query");

        DB::statement($query);

        $now = Carbon::now();
        $this->log("       - END: at " . $now->toDateTimeString() . " | Started = " . $now->diffForHumans($initialDate));
    }

    /**
     * Consulta las columnas de una tabla desde la BD. Crea el formato (col1, col2, ..., colN)
     * necesario para realizar un INSERT INTO ...
     *
     * @param $table
     * @return string
     */
    function getTableColumns($table)
    {
        $columns = collect(DB::select("SELECT column_name FROM information_schema.columns WHERE table_name   = '$table' ORDER BY ordinal_position"))->pluck('column_name');

        return "(".$columns->implode(', ').")";
    }

    /**
     * Ejecuta el script de linux para crear el backup SQL de la tabla y cargarlo al s3
     *
     * @param $table
     * @param $from
     * @param $to
     * @return bool
     */
    public function processBackup($table, $from, $to)
    {
        $this->log("   Processing backup table: $table...");
        $tableBackup = $this->getTableBackup($table, $to);

        $script = shell_exec("cd app/Console/Shell/DB/Maintenance && /bin/bash backup.sh $tableBackup $from $to");
        $this->log("       - $script");

        return Str::contains($script, "true");
    }

    /**
     * Elimina la tabla backup de la BD
     *
     * @param $table
     * @param $from
     * @param $to
     * @return void
     */
    public function releaseTable($table, $from, $to)
    {
        $tableBackup = $this->getTableBackup($table, $to);
        $query = "DROP TABLE IF EXISTS $tableBackup";
        $this->log("       - $query");
        DB::statement($query);
    }

    /**
     * Ej locations_2030_01_27_current
     *
     * @param $table
     * @param $date
     * @return string
     */
    public function getTableBackup($table, $date)
    {
        $tableBackupDate = Str::replaceArray('-', ['_', '_'], $date);
        return $table . "_" . $tableBackupDate;
    }

    public function log($message)
    {
        \Log::info($message);
        $this->info($message);
    }
}
