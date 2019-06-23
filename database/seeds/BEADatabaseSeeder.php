<?php

use Illuminate\Database\Seeder;

class BEADatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->dropTables([
            'bea_takings',
            'bea_liquidations',
            'bea_commissions',
            'bea_penalties',
            'bea_discounts',
            'bea_discount_types'
        ]);

        DB::transaction(function () {
            $this->call(DiscountTypesTableSeeder::class);
            $this->call(DiscountsTableSeeder::class);
            $this->call(PenaltiesTableSeeder::class);
            $this->call(CommissionsTableSeeder::class);
        });
    }

    /**
     * @param $tables
     */
    function dropTables($tables)
    {
        foreach ($tables as $table) {
            DB::statement("TRUNCATE $table CASCADE");
        }
    }
}
