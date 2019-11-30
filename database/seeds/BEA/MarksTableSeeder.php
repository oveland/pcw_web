<?php

use App\Facades\BEADB;
use App\Models\BEA\Mark;
use App\Services\BEA\BEASyncService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MarksTableSeeder extends Seeder
{
    /**
     * @var BEASyncService
     */
    private $sync;

    public function __construct(BEASyncService $sync)
    {

        $this->sync = $sync;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $this->sync->marks();
    }
}
