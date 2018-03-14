<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;

class DatabaseManageLocationReports extends Command
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
        $numberSegments = config('maintenance.number_segments');
        $daysPerSegment = config('maintenance.day_per_segment');

        $this->info("Segmenting tables: 'locations' and 'reports'");
        $this->info("Number Segments: $numberSegments, Days Per Segment: $daysPerSegment");

        foreach (range(1, $numberSegments) as $segment) {
            $this->info("Segmenting number: $segment...");

            $interval = $segment * $daysPerSegment;
            $lastInterval = ($segment - 1) * $daysPerSegment;

            DB::statement("TRUNCATE TABLE location_reports_$segment");

            DB::statement("
                INSERT INTO location_reports_$segment
                SELECT l.id AS location_id,
                  l.dispatch_register_id,
                  l.off_road,
                  l.latitude,
                  l.longitude,
                  r.date,
                  l.date AS location_date,
                  r.timed,
                  r.distancem,
                  r.status_in_minutes                
                FROM locations l
                  JOIN reports r ON (r.location_id = l.id AND r.date > current_date - $interval AND r.date <= ((current_date - $lastInterval)||' 23:59:59')::TIMESTAMP)
                WHERE l.date > current_date - $interval AND l.date <= ((current_date - $lastInterval)||' 23:59:59')::TIMESTAMP                
            ");
        }

        $this->info("End segmentation process!");
    }
}
