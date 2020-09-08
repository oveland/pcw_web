<?php

namespace App\Console\Commands\Tools;

use App\Models\Company\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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

        switch ($companyId) {
            case Company::MONTEBELLO:
                $maxMileage = 900000;
                break;
            case Company::YUMBENOS:
                $maxMileage = 500000;
                break;
            default:
                $maxMileage = 400000;
                break;
        }

        $query = "
            SELECT id, date::DATE, vehicle_id, current_mileage, odometer, yesterday_odometer FROM last_locations
            WHERE (current_mileage <= 0 or current_mileage > $maxMileage)
            AND date > '$from'
            AND odometer <> yesterday_odometer
            AND vehicle_id IN (SELECT id FROM vehicles WHERE company_id = $companyId)
            limit 10000
        ";

        dump($query);

        $lastLocations = \DB::select($query);

        foreach ($lastLocations as $lastLocation) {
            $this->info("- $lastLocation->id > $lastLocation->current_mileage m...");
            $queryIO = "
                select odometer
                FROM locations
                where date between ('$lastLocation->date 00:00:00')::timestamp without time zone and ('$lastLocation->date 23:59:59')::timestamp without time zone
                  and vehicle_id = $lastLocation->vehicle_id
                  and odometer > 1000
                order by date ASC
                LIMIT 1;
            ";
            $initialOdometer = collect(collect(\DB::select($queryIO))->first())->get('odometer');

            $this->info("               initialOdometer = $initialOdometer");

            if ($initialOdometer) {
                $queryFO = "
                    select odometer
                    FROM locations
                    where date between ('$lastLocation->date 00:00:00')::timestamp without time zone and ('$lastLocation->date 23:59:59')::timestamp without time zone
                      and vehicle_id = $lastLocation->vehicle_id
                      and odometer > 1000
                    order by date DESC
                    LIMIT 1;
                ";
                $finalOdometer = collect(collect(\DB::select($queryFO))->first())->get('odometer');

                $this->info("               finalOdometer = $finalOdometer");

                if ($finalOdometer) {
                    $cm = intval($finalOdometer - $initialOdometer);

                    $this->info("               diff = $cm");

                    if ($cm > 0 && $cm < $maxMileage) {
                        $this->info("   > Before $lastLocation->current_mileage Then $cm on id = $lastLocation->id ");
                        \DB::statement("UPDATE last_locations SET current_mileage = $cm WHERE id = $lastLocation->id");
                    }
                }
            }
        }
    }
}
