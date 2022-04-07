<?php

namespace App\Console\Commands\DB;

use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MaintenanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:maintenance {--from=} {--to=}';

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

    public function getMaintenanceData()
    {
        $from = $this->option('from');
        $to = $this->option('to');

        dd(['Ejecuta comando', 'Ejecuta comando', 'Ejecuta comando']);

        return collect([
//            [
//                'from' => $from,
//                'to' => $to,
//                'tables' => [
//                    'locations' => [
//                        'release' => true,
//                        'hasBackup' => false,
//                    ],
//                    'reports' => [
//                        'release' => true,
//                        'hasBackup' => false,
//                    ],
//                ]
//            ],
            [
                'from' => '2022-01-01',
                'to' => '2022-01-31',
                'tables' => [
                    'locations' => [
                        'release' => false,
                        'hasBackup' => false,
                        'restore' => false,
                    ],
                    'reports' => [
                        'release' => false,
                        'hasBackup' => false,
                        'restore' => false,
                    ],
                ]
            ],
            [
                'from' => '2022-02-01',
                'to' => '2022-02-28',
                'tables' => [
                    'locations' => [
                        'release' => false,
                        'hasBackup' => false,
                        'restore' => false,
                    ],
                    'reports' => [
                        'release' => false,
                        'hasBackup' => false,
                        'restore' => false,
                    ],
                ]
            ],
            [
                'from' => '2022-03-01',
                'to' => '2022-01-31',
                'tables' => [
                    'locations' => [
                        'release' => false,
                        'hasBackup' => false,
                        'restore' => false,
                    ],
                    'reports' => [
                        'release' => false,
                        'hasBackup' => false,
                        'restore' => false,
                    ],
                ]
            ],
        ]);
    }

    function getTableColumns($table)
    {
        $columns = "";

        switch ($table) {
            case 'locations':
                $columns = "(id, version, date, date_created, dispatch_register_id, distance, last_updated, latitude, longitude, odometer,orientation, speed, status, vehicle_id, off_road, vehicle_status_id, speeding, current_mileage, ard_off_road)";
                break;
            case 'reports':
                $columns = "(id, version, date, date_created, dispatch_register_id, distanced, distancem, distancep, last_updated, status, timed, timem, timep, location_id, status_in_minutes, control_point_id, fringe_id)";
                break;
        }

        return $columns;
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

        $maintenanceData = $this->getMaintenanceData();

        $maintenanceData->each(function ($maintenance) {
            $maintenance = json_decode(json_encode($maintenance), FALSE); // To Object
            $this->process($maintenance);
        });

//        $query = "INSERT INTO locations (id, version, date, date_created, dispatch_register_id, distance, last_updated, latitude, longitude, odometer,orientation, speed, status, vehicle_id, off_road, vehicle_status_id, speeding, current_mileage, ard_off_road) SELECT * FROM locations_0 WHERE date < current_date";
//        $this->log($query);
//        DB::statement($query);

        $this->log("Maintenance finished at " . Carbon::now()->toDateTimeString() . " | From " . Carbon::now()->diffForHumans($initialDate));

    }

    public function process($maintenance)
    {
        $tables = $maintenance->tables;

        if ($maintenance->from && $maintenance->to && $maintenance->from < $maintenance->to) {
            foreach ($tables as $table => $options) {
                $hasBackup = isset($options->hasBackup) ? $options->hasBackup : false;
                $this->processTable($table, $maintenance->from, $maintenance->to, $options->release, $hasBackup, $options->restore);
            }
        } else {
            $this->log("Invalid date range From: $maintenance->from and To: $maintenance->to");
        }
    }

    public function processTable($table, $from, $to, $release = false, $hasBackup = false, $restore = false)
    {
        $this->log("   Processing table: $table...");
        $tableBackup = $this->getTableBackup($table, $to);

        if ($restore) {
            $this->restore($table, $from, $to);
        } else {
            if (!$hasBackup) {
//                DB::statement("DROP TABLE IF EXISTS $tableBackup");
                $query = "CREATE TABLE $tableBackup AS SELECT * FROM $table WHERE date BETWEEN '$from' AND '$to'";
                $this->log("       - $query");
                DB::statement($query);
            }

            $backupSuccess = $this->buildBackup($table, $from, $to);
            if ($release && $backupSuccess) {
                $this->releaseTable($table, $from, $to);
            }
        }
    }

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

    public function buildBackup($table, $from, $to)
    {
        $this->log("   Processing backup table: $table...");
        $tableBackup = $this->getTableBackup($table, $to);

        $script = shell_exec("cd app/Console/Shell/DB/Maintenance && /bin/bash backup.sh $tableBackup $from $to");
        $this->log("       - $script");

        return $script === 'true' || $script === true;
    }

    public function releaseTable($table, $from, $to)
    {
        $tableBackup = $this->getTableBackup($table, $to);
        $query = "DROP TABLE IF EXISTS $tableBackup";
        $this->log("       - $query");
        DB::statement($query);
    }

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
