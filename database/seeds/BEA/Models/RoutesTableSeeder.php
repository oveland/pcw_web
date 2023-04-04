<?php

use App\Models\Company\Company;
use App\Services\LM\Sources\BEA\DEFSyncService;
use Illuminate\Database\Seeder;

class RoutesTableSeeder extends Seeder
{
    /**
     * @var \App\Services\LM\Sources\BEA\DEFSyncService
     */
    private $sync;

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
        $this->sync->routes();
    }
}
