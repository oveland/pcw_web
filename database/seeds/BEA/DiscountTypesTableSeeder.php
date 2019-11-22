<?php

use App\Models\BEA\DiscountType;
use Illuminate\Database\Seeder;

class DiscountTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $types = [
            'fuel' => (object)[
                'id' => 0,
                'icon' => 'fa fa-tachometer',
                'min' => 30000,
                'max' => 36000,
            ],
            'tolls' => (object)[
                'id' => 1,
                'icon' => 'fa fa-ticket',
                'min' => 8000,
                'max' => 12000,
            ],
            'washing' => (object)[
                'id' => 2,
                'icon' => 'fa fa-tint',
                'min' => 20000,
                'max' => 30000,
            ],
        ];

        foreach ($types as $name => $type) {
            DiscountType::create([
                'name' => __(ucfirst($name)),
                'icon' => $type->icon,
                'description' => __('Discount by') . " ".__(ucfirst($name)),
                'default' => random_int($type->min, $type->max)
            ]);
        }

        DiscountType::create([
            'name' => __('Mobility auxilio'),
            'icon' => 'fa fa-user text-warning',
            'description' => __('Discount by') .' '. __('Mobility auxilio'),
            'default' => random_int(2000, 5000)
        ]);
    }
}
