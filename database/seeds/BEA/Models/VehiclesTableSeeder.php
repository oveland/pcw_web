<?php

use App\Facades\BEADB;
use App\Models\LM\Discount;
use App\Models\LM\Penalty;
use App\Models\Company\Company;
use App\Models\Vehicles\Vehicle;
use App\Services\BEA\BEASyncService;
use Illuminate\Database\Seeder;

class VehiclesTableSeeder extends Seeder
{
    /**
     * @var BEASyncService
     */
    private $sync;

    /**
     * VehiclesTableSeeder constructor.
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
        $this->sync->vehicles();
    }
}
