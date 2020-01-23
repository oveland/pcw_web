<?php

use App\Models\BEA\DiscountType;
use App\Models\Company\Company;
use App\Services\BEA\BEARepository;
use Illuminate\Database\Seeder;

class DiscountTypesTableSeeder extends Seeder
{
    /**
     * @var BEARepository
     */
    private $repository;

    /**
     * DiscountTypesTableSeeder constructor.
     * @param BEARepository $repository
     */
    public function __construct(BEARepository $repository)
    {
        $this->repository = $repository;
        $this->repository->company = Company::find(Company::PAPAGAYO);
    }

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $types = [
            'Mobility auxilio' => (object)[
                'uid' => 1,
                'icon' => 'fa fa-user text-warning',
                'min' => 2000,
                'max' => 5000,
            ],
            'Fuel' => (object)[
                'uid' => 2,
                'icon' => 'fa fa-tachometer',
                'min' => 30000,
                'max' => 36000,
            ],
            'Operative Expenses' => (object)[
                'uid' => 3,
                'icon' => 'fa fa-hint text-warning',
                'min' => 2000,
                'max' => 5000,
            ],
            'Tolls' => (object)[
                'uid' => 4,
                'icon' => 'fa fa-ticket',
                'min' => 8000,
                'max' => 12000,
            ]
        ];

        foreach ($types as $name => $type) {
            $exists = DiscountType::where('company_id', $this->repository->company->id)->where('uid', $type->uid)->first();

            if (!$exists) {
                DiscountType::create([
                    'uid' => $type->uid,
                    'name' => __(ucfirst($name)),
                    'icon' => $type->icon,
                    'description' => __('Discount by') . " " . __(ucfirst($name)),
                    'default' => random_int($type->min, $type->max),
                    'company_id' => $this->repository->company->id
                ]);
            }
        }
    }
}
