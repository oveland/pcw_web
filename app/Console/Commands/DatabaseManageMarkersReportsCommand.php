<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;

class DatabaseManageMarkersReportsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:manage-markers-reports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh materialized view for markers reports';

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
        $dbConnectionMonth = 'GPS_MONTH';
        $this->info("Refreshing views for Markers Month...");

        foreach (range(1, 10) as $segment) {
            DB::connection($dbConnectionMonth)->statement("REFRESH MATERIALIZED VIEW markers_historial_mes_$segment");
            $this->info("Refreshed MH_M_$segment");
        }
    }
}
