<?php

namespace App\Console\Commands\DB;

use DB;
use Illuminate\Console\Command;

class RefreshLocationsViews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:refresh-locations-views';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh the materialized views from locations table';

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
        $tables = config('database.maintenance.locations.fragments.tables');

        foreach (range(1, $tables) as $table) {
            $viewName = "locations_$table";
            $sql = "REFRESH MATERIALIZED VIEW $viewName";
            DB::statement($sql);
            $this->info($sql);
        }

        $sql = "TRUNCATE TABLE locations_0";
        DB::statement($sql);
        $this->info($sql);
    }
}
