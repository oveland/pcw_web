<?php

use App\Facades\BEADB;
use App\Models\BEA\Mark;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MarksTableSeeder extends Seeder
{
    public function __construct()
    {

    }

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $lastIdMigrated = Mark::max('id');
        $marks = BEADB::select("SELECT * FROM A_MARCA WHERE AMR_IDMARCA > $lastIdMigrated");

        foreach ($marks as $markBEA) {
            $mark = $this->processMark($markBEA);

            if (!$mark->save()) {
                throw new Exception("Error saving MARK with id: $markBEA->AMR_IDMARCA");
            }
        }
    }

    /**
     * @param $markBEA
     * @return Mark
     */
    function processMark($markBEA)
    {
        //$mark = Mark::find($markBEA->AMR_IDMARCA);
        
        //if (!$mark)
        $mark = new Mark();

        $passengersUp = $markBEA->AMR_SUBIDAS;
        $passengersDown = $markBEA->AMR_BAJADAS;

        $locks = $markBEA->AMR_BLOQUEOS;
        $auxiliaries = $markBEA->AMR_AUXILIARES;

        $imBeaMax = $markBEA->AMR_IMEBEAMAX;
        $imBeaMin = $markBEA->AMR_IMEBEAMIN;

        $passengersBoarding = $passengersUp > $passengersDown ? ($passengersUp - $passengersDown) : 0;

        $passengersBEA = $passengersUp > $passengersDown ? $passengersUp : $passengersDown;
        $totalBEA = (($imBeaMax + $imBeaMin) / 2) * 2500;

        $mark->id = $markBEA->AMR_IDMARCA;
        $mark->turn_id = $markBEA->AMR_IDTURNO;
        $mark->trajectory_id = $markBEA->AMR_IDDERROTERO;
        $mark->date = Carbon::createFromFormat("Y-m-d H:i:s", $markBEA->AMR_FHINICIO);
        $mark->initial_time = Carbon::createFromFormat("Y-m-d H:i:s", $markBEA->AMR_FHINICIO);
        $mark->final_time = Carbon::createFromFormat("Y-m-d H:i:s", $markBEA->AMR_FHFINAL);
        $mark->passengers_up = $passengersUp;
        $mark->passengers_down = $passengersDown;
        $mark->locks = $locks;
        $mark->auxiliaries = $auxiliaries;
        $mark->boarded = $passengersBoarding;
        $mark->im_bea_max = $imBeaMax;
        $mark->im_bea_min = $imBeaMin;
        $mark->total_bea = ceil($totalBEA);
        $mark->passengers_bea = $passengersBEA;

        return $mark;
    }
}
