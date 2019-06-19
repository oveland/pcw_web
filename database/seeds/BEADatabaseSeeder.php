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

        /* Table from BEA */
        DB::transaction(function () {
            $this->call(RoutesTableSeeder::class);
            $this->call(VehiclesTableSeeder::class);
            $this->call(DriversTableSeeder::class);

            $this->call(TrajectoriesTableSeeder::class);
            $this->call(TurnsTableSeeder::class);
        });

        DB::transaction(function () {
            /* Tables from NE */
            $this->call(DiscountTypesTableSeeder::class);
            //$this->call(DiscountsTableSeeder::class);
            $this->call(PenaltiesTableSeeder::class);
            $this->call(CommissionsTableSeeder::class);
            $this->call(MarksTableSeeder::class);
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
