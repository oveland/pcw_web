<?php

use App\Facades\BEADB;
use App\Models\BEA\Turn;
use Illuminate\Database\Seeder;

class TurnsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $turns = BEADB::select("SELECT * FROM A_TURNO WHERE ATR_FECHATURNO >= '2019-03-01'");

        foreach ($turns as $turnBEA) {
            $turn = Turn::find($turnBEA->ATR_IDTURNO);
            if (!$turn) $turn = new Turn();

            $turn->id = $turnBEA->ATR_IDTURNO;
            $turn->vehicle_id = $turnBEA->ATR_IDAUTOBUS;
            $turn->route_id = $turnBEA->ATR_IDRUTA;
            $turn->driver_id = $turnBEA->ATR_IDCONDUCTOR;

            if (!$turn->save()) {
                throw new Exception("Error saving TURN with id: $turnBEA->ATR_IDTURNO");
            }
        }
    }
}
