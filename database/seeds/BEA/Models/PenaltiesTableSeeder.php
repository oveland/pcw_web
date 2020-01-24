<?php

use App\Models\Company\Company;
use App\Services\BEA\BEASyncService;
use Illuminate\Database\Seeder;

class PenaltiesTableSeeder extends Seeder
{
    /**
     * @var Company
     */
    private $company;
    /**
     * @var BEASyncService
     */
    private $sync;

    /**
     * DiscountsTableSeeder constructor.
     * @param BEASyncService $sync
     */
    public function __construct(BEASyncService $sync)
    {
        $this->company = Company::find(Company::PAPAGAYO);
        $this->sync = $sync;
        $this->sync->company = $this->company;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $vehicles = $this->company->activeVehicles;

        foreach ($vehicles as $vehicle) {
            $this->sync->checkPenaltiesFor($vehicle);
        }
    }
}
