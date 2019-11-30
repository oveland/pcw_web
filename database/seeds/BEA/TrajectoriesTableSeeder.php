<?php

use App\Services\BEA\BEASyncService;
use Illuminate\Database\Seeder;

class TrajectoriesTableSeeder extends Seeder
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
        $this->sync->trajectories();
    }
}
