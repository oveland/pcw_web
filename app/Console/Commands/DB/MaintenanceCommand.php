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
    protected $signature = 'db:maintenance';

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
        return collect([
            [
                'from' => '2020-03-15',
                'to' => '2020-03-31',
                'tables' => [
                    'locations' => [
                        'release' => true
                    ],
                    'reports' => [
                        'release' => true
                    ],
                ]
            ],
//            [
//                'from' => '2020-04-01',
//                'to' => '2020-04-30',
//                'tables' => [
//                    'locations' => [
//                        'release' => true
//                    ],
//                    'reports' => [
//                        'release' => true
//                    ],
//                ]
//            ],
//            [
//                'from' => '2020-05-01',
//                'to' => '2020-05-31',
//                'tables' => [
//                    'locations' => [
//                        'release' => true
//                    ],
//                    'reports' => [
//                        'release' => true
//                    ],
//                ]
//            ],
//            [
//                'from' => '2020-06-01',
//                'to' => '2020-06-30',
//                'tables' => [
//                    'locations' => [
//                        'release' => true
//                    ],
//                    'reports' => [
//                        'release' => true
//                    ],
//                ]
//            ],
//            [
//                'from' => '2020-07-01',
//                'to' => '2020-07-31',
//                'tables' => [
//                    'locations' => [
//                        'release' => false
//                    ],
//                    'reports' => [
//                        'release' => false
//                    ],
//                ]
//            ],
//            [
//                'from' => '2020-08-01',
//                'to' => '2020-08-31',
//                'tables' => [
//                    'locations' => [
//                        'release' => false
//                    ],
//                    'reports' => [
//                        'release' => false
//                    ],
//                ]
//            ],
//            [
//                'from' => '2020-09-01',
//                'to' => '2020-09-30',
//                'tables' => [
//                    'locations' => [
//                        'release' => false
//                    ],
//                    'reports' => [
//                        'release' => false
//                    ],
//                ]
//            ],
        ]);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $now = Carbon::now();
        $this->info('Executing db maintenance at ' . $now->toDateTimeString());

        $maintenanceData = $this->getMaintenanceData();

        $maintenanceData->each(function ($maintenance) {
            $maintenance = json_decode(json_encode($maintenance), FALSE); // To Object
            $this->process($maintenance);
        });

        $this->info("Script response: ");

    }

    public function process($maintenance)
    {
        $tables = $maintenance->tables;

        foreach ($tables as $table => $options) {
            $this->processTable($table, $maintenance->from, $maintenance->to, $options->release);
        }
    }

    public function processTable($table, $from, $to, $release = false)
    {
        $this->info("   Processing table: $table...");
        $tableBackup = $this->getTableBackup($table, $to);

        $query = "CREATE TABLE $tableBackup AS SELECT * FROM $table WHERE date BETWEEN '$from' AND '$to'";
        $this->info("       - $query");

        DB::statement($query);

        $this->buildBackup($table, $from, $to);

        $query = "DROP TABLE $tableBackup";
        $this->info("       - $query");

        if ($release) {
            $this->releaseTable($table, $from, $to);
        }
    }

    public function buildBackup($table, $from, $to)
    {
        $this->info("   Processing backup table: $table...");
        $tableBackup = $this->getTableBackup($table, $to);

        $script = shell_exec("cd app/Console/Shell/DB/Maintenance && sh backup.sh $tableBackup $from $to");
        $this->info("       - $script");
    }

    public function releaseTable($table, $from, $to)
    {
        $this->info("   Releasing dats from table: $table...");

        $query = "DELETE FROM $table WHERE date BETWEEN '$from' AND '$to'";
        $this->info("       - $query");

        $tableBackup = $this->getTableBackup($table, $to);
        $query = "DROP TABLE IF EXISTS $tableBackup";
        $this->info("       - $query");


        DB::statement($query);
    }

    public function getTableBackup($table, $date)
    {
        $tableBackupDate = Str::replaceArray('-', ['_', '_'], $date);
        return $table . "_" . $tableBackupDate;
    }
}