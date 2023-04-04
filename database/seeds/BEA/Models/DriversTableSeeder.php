<?php

use App\Models\Company\Company;
use App\Services\LM\Sources\BEA\DEFSyncService;
use Illuminate\Database\Seeder;

class DriversTableSeeder extends Seeder
{
    /**
     * @var \App\Services\LM\Sources\BEA\DEFSyncService
     */
    private $sync;

    /**
     * DriversTableSeeder constructor.
     * @param DEFSyncService $sync
     */
    public function __construct(DEFSyncService $sync)
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
