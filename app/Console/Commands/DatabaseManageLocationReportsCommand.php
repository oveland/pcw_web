<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;

class DatabaseManageLocationReportsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:manage-location-reports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command feds locations_reports_X tables';

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
        /* Update segmented tables */
        $numberSegments = config('maintenance.number_segments');
        $daysPerSegment = config('maintenance.day_per_segment');

        $this->info("Segmenting tables: 'locations' and 'reports'");
        $this->info("Number Segments: $numberSegments, Days Per Segment: $daysPerSegment");

        foreach (range(1, $numberSegments) as $segment) {
            $this->info("Refreshing materialized view location_reports_$segment...");
            DB::statement("REFRESH MATERIALIZED VIEW location_reports_$segment");
        }

        /* Clear current locations reports data */
        DB::statement("TRUNCATE TABLE current_location_reports");


        $this->info("End segmentation process!");
    }
}
