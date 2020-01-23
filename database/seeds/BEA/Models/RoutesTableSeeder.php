<?php

use App\Facades\BEADB;
use App\Models\Company\Company;
use App\Models\Routes\Route;
use App\Services\BEA\BEASyncService;
use Illuminate\Database\Seeder;

class RoutesTableSeeder extends Seeder
{
    /**
     * @var BEASyncService
     */
    private $sync;

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
        $this->sync->routes();
    }
}
