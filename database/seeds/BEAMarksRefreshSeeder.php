<?php

use Illuminate\Database\Seeder;

class BEAMarksRefreshSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Throwable
     */
    public function run()
    {
        DB::transaction(function () {
            $this->call(TurnsTableSeeder::class);
            $this->call(TrajectoriesTableSeeder::class);
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
