<?php

use App\Facades\BEADB;
use App\Models\BEA\Trajectory;
use App\Models\Routes\Route;
use Illuminate\Database\Seeder;

class TrajectoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $trajectories = BEADB::select("SELECT * FROM C_DERROTERO");

        foreach ($trajectories as $trajectoryBEA) {
            $trajectory = Trajectory::find($trajectoryBEA->CDR_IDDERROTERO);
            $route = Route::where('bea_id', $trajectoryBEA->CDR_IDRUTA)->get()->first();
            if (!$trajectory) $trajectory = new Trajectory();

            $trajectory->id = $trajectoryBEA->CDR_IDDERROTERO;
            $trajectory->name = $trajectoryBEA->CDR_DESCRIPCION;
            $trajectory->route_id = $route->id;
            $trajectory->description = "$trajectoryBEA->CDR_DESCRIPCION";

            if (!$trajectory->save()) {
                throw new Exception("Error saving TRAJECTORY with id: $trajectoryBEA->CDR_IDDERROTERO");
            }
        }
    }
}