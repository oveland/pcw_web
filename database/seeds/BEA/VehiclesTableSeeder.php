<?php

use App\Facades\BEADB;
use App\Models\Company\Company;
use App\Models\Vehicles\Vehicle;
use Illuminate\Database\Seeder;

class VehiclesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $maxSequence = collect(\DB::select("SELECT max(id_crear_vehiculo) max FROM crear_vehiculo"))->first()->max + 1;
        DB::statement("ALTER SEQUENCE vehicles_id_seq RESTART WITH $maxSequence");

        $vehicles = BEADB::select("SELECT * FROM C_AUTOBUS");

        foreach ($vehicles as $vehicleBEA) {
            $vehicle = Vehicle::where('bea_id', $vehicleBEA->CAU_IDAUTOBUS)->get()->first();
            if (!$vehicle) $vehicle = new Vehicle();

            $duplicatedPlates = BEADB::select("SELECT count(1) TOTAL FROM C_AUTOBUS WHERE CAU_PLACAS = '$vehicleBEA->CAU_PLACAS'")->first();

            if ($duplicatedPlates->TOTAL > 1) $vehicleBEA->CAU_PLACAS = "$vehicleBEA->CAU_PLACAS-$vehicleBEA->CAU_NUMECONOM";

            if ($vehicleBEA->CAU_PLACAS) {
                $vehicle->bea_id = $vehicleBEA->CAU_IDAUTOBUS;
                $vehicle->plate = $vehicleBEA->CAU_PLACAS;
                $vehicle->number = $vehicleBEA->CAU_NUMECONOM;
                $vehicle->company_id = Company::COODETRANS;
                $vehicle->active = true;
                $vehicle->in_repair = false;

                if (!$vehicle->save()) {
                    throw new Exception("Error saving VEHICLE with id: $vehicleBEA->CAU_IDAUTOBUS");
                }
            }
        }

        $maxSequence = collect(\DB::select("SELECT max(id) max FROM vehicles"))->first()->max + 1;
        DB::statement("ALTER SEQUENCE crear_vehiculo_id_crear_vehiculo_seq RESTART WITH $maxSequence");
    }
}
