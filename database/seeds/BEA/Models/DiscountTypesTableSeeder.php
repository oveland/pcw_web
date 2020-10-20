<?php

use App\Models\BEA\DiscountType;
use App\Models\Company\Company;
use App\Services\BEA\BEARepository;
use App\Services\BEA\BEASyncService;
use Illuminate\Database\Seeder;

class DiscountTypesTableSeeder extends Seeder
{
    /**
     * @var BEASyncService
     */
    private $sync;

    /**
     * DiscountTypesTableSeeder constructor.
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
        $this->sync->discountTypes();
    }
}
