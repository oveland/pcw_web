<?php

use App\Models\BEA\Commission;
use App\Models\Company\Company;
use App\Services\BEA\BEASyncService;
use Illuminate\Database\Seeder;

class CommissionsTableSeeder extends Seeder
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
        $this->sync->commissions();
    }
}
