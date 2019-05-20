<?php

use App\Models\BEA\Mark;
use Illuminate\Database\Seeder;

class MarksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (range(1,50) as $i){
            $mark = factory(Mark::class)->make()->save();
        }
    }
}
