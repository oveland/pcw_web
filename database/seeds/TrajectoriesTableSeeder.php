<?php

use App\Models\BEA\Trajectory;
use Illuminate\Database\Seeder;

class TrajectoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Trajectory::create([
            'name' => 'IDA',
            'description' => 'Trayectoria de IDA',
        ]);

        Trajectory::create([
            'name' => 'REGRESO',
            'description' => 'Trayectoria de REGRESO',
        ]);
    }
}
