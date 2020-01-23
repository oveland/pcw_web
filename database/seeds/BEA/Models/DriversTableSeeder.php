<?php

use App\Facades\BEADB;
use App\Models\Company\Company;
use App\Models\Drivers\Driver;
use App\Services\BEA\BEASyncService;
use Illuminate\Database\Seeder;

class DriversTableSeeder extends Seeder
{
    /**
     * @var BEASyncService
     */
    private $sync;

    /**
     * DriversTableSeeder constructor.
     * @param BEASyncService $sync
     */
    public function __construct(BEASyncService $sync)
    {
        $this->sync = $sync;
        $this->sync->company = Company::find(Company::PAPAGAYO);
    }

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $this->sync->drivers();
    }
}
