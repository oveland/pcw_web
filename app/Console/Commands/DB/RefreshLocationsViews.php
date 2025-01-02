<?php

namespace App\Console\Commands\DB;

use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

class RefreshLocationsViews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:refresh-locations-views {--truncate-locations-0}';

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
        $truncateLocation0 = $this->option('truncate-locations-0');

        $now = Carbon::now();
        $this->info('Executing db refresh locations views at ' . $now->toDateTimeString());

        $tables = config('database.maintenance.locations.fragments.tables');

        foreach (range(1, $tables) as $table) {
            $viewName = "locations_$table";
            $sql = "REFRESH MATERIALIZED VIEW $viewName";
            DB::statement($sql);
            $this->info($sql);
        }

        if($truncateLocation0){
            $sql = "TRUNCATE TABLE locations_0";
            DB::statement($sql);
            $this->info($sql);

            $sql = "TRUNCATE TABLE app_photos_0";
            DB::statement($sql);
            $this->info($sql);
        }
        $this->info("Refresh locations views finished at " . Carbon::now()->toDateTimeString());
    }
}
