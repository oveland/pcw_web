<?php

namespace App\Console\Commands\Tools;

use Illuminate\Console\Command;

class FixMileageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tools:fix-mileage {--company=14} {--from=2020-01-01}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and fix high or negative mileage values';

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
        $companyId = $this->option('company');
        $from = $this->option('from');
        $maxMileage = 500000;

        $lastLocations = \DB::select("
            SELECT id, date::DATE, vehicle_id, current_mileage, odometer, yesterday_odometer FROM last_locations
            WHERE (odometer = 0 OR (current_mileage > $maxMileage OR current_mileage <= 0)) 
            AND date > '$from'
            AND vehicle_id IN (SELECT id FROM vehicles WHERE company_id = $companyId)
limit 5
        ");

        foreach ($lastLocations as $lastLocation) {
            $queryCM = "
                select current_mileage
                FROM locations
                where date between ('$lastLocation->date 00:00:00')::timestamp without time zone and ('$lastLocation->date 23:59:59')::timestamp without time zone
                  and vehicle_id = $lastLocation->vehicle_id
                  and current_mileage > 0
                order by date DESC
                LIMIT 1;
            ";

            $cm = collect(\DB::select($queryCM))->first();

            dump($lastLocation, "---> ", $cm, "", "");
//            $this->info("$lastLocation->id ");
        }
    }
}
