<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;

class DatabaseSegmentReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:segment-reports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Segments tables locations and reports';

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

            DB::statement("DROP TABLE IF EXISTS location_reports_$segment");
            DB::statement("
                CREATE TABLE location_reports_$segment AS 
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
                  JOIN reports r ON (r.location_id = l.id AND r.date > current_date - $interval AND r.date <= current_date - $lastInterval)
                WHERE l.date > current_date - $interval AND l.date <= current_date - $lastInterval
            ");
        }

        $this->info("End segmentation process!");
    }
}
