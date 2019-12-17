<?php

use App\Facades\BEADB;
use App\Models\BEA\Discount;
use App\Models\BEA\Penalty;
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
            $vehicle = Vehicle::where('bea_id', $vehicleBEA->CAU_IDAUTOBUS)->where('company_id', Company::COODETRANS)->first();
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
                }else{
                    $this->checkParams($vehicle);
                }
            }
        }

        $maxSequence = Vehicle::max('id') + 1;
        DB::statement("ALTER SEQUENCE crear_vehiculo_id_crear_vehiculo_seq RESTART WITH $maxSequence");
    }

    public function checkParams(Vehicle $vehicle)
    {
        $referenceVehicle = Vehicle::find(1946);

        $ok = true;
        if($vehicle){
            $discounts = Discount::where('vehicle_id', $referenceVehicle->id)->get();
            foreach ($discounts as $discount){
                $exists = Discount::where('vehicle_id', $vehicle->id)->where('route_id', $discount->route->id)->where('trajectory_id', $discount->trajectory_id)->where('discount_type_id', $discount->discount_type_id)->first();

                if(!$exists){
                    $new = new Discount();
                    $new->vehicle_id = $vehicle->id;
                    $new->discount_type_id = $discount->discount_type_id;
                    $new->route_id = $discount->route_id;
                    $new->trajectory_id = $discount->trajectory_id;
                    $new->value = $discount->value;

                    if(!$new->save())$ok = false;
                }
            }

            $penalties = Penalty::where('vehicle_id', $referenceVehicle->id)->get();
            foreach ($penalties as $penalty){
                $exists = Penalty::where('vehicle_id', $vehicle->id)->where('route_id', $penalty->route->id)->first();

                if(!$exists){
                    $new = new Penalty();
                    $new->vehicle_id = $vehicle->id;
                    $new->route_id = $penalty->route_id;
                    $new->type = $penalty->type;
                    $new->value = $penalty->value;

                    if(!$new->save())$ok = false;
                }
            }
        }

        return $ok;
    }
}
