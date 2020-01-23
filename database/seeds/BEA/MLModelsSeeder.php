<?php

use Illuminate\Database\Seeder;

class MLModelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Throwable
     */
    public function run()
    {
        /*$this->dropTables([
            'bea_marks',
            'bea_turns',
            'bea_trajectories'
        ]);*/

        /* Table from BEA */
        DB::transaction(function () {
            $this->call(RoutesTableSeeder::class);
            $this->call(VehiclesTableSeeder::class);
            $this->call(DriversTableSeeder::class);
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
