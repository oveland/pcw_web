<?php

namespace App\Services\Reports\Routes\Takings;

use App\Models\Company\Company;
use App\Models\Operation\FuelStation;
use App\Models\Routes\DispatchRegister;
use App\Models\Routes\RouteTaking;
use Auth;
use DB;
use Exception;

class RouteTakingsService
{
    /**
     * @param DispatchRegister $dispatchRegister
     * @param array $data
     * @return object
     */
    function taking(DispatchRegister $dispatchRegister, array $data = [])
    {
        $response = (object)['success' => false, 'message' => ''];

        $takings = $dispatchRegister->takings;
        $takings->fill($data);

//        $takings->taken = $takings->total_production > 0;
        $takings->taken = true;
        $takings->balance = $takings->net_production - $takings->advance;

        $takings->user()->associate(Auth::user());

        if ($takings->save()) {
            if ($this->processPassengersTaken($dispatchRegister)) {
                $response->success = true;
                $response->message = __('Takings registered successfully');
                $response->taken = $takings->taken;
            }
        } else {
            $response->message = __('Takings not registered');
        }

        return $response;
    }

    /**
     * @param DispatchRegister $dispatchRegister
     * @return object
     */
    function deleteTakings(DispatchRegister $dispatchRegister)
    {
        $response = (object)['success' => false, 'message' => ''];

        $takings = $dispatchRegister->takings;

        try {
            if ($takings->delete()) {
                $response->success = true;

                DB::statement("UPDATE registrodespacho SET initial_sensor_recorder = 0, final_sensor_recorder = 0, ignore_trigger = TRUE WHERE id_registro = $dispatchRegister->id");

                $response->message = __('Takings deleted successfully');
            } else {
                $response->message = __('Takings not deleted');
            }
        } catch (Exception $exception) {
            $response->message = __('Error deleting takings') . " > " . $exception->getMessage();
        }

        return $response;
    }

    function getFuelStations(Company $company)
    {
        return FuelStation::allByCompany($company);
    }

    private function processPassengersTaken(DispatchRegister $dispatchRegister)
    {
        $dispatchRegister->refresh();
        $takings = $dispatchRegister->takings;
        $passengersTaken = $takings->getPassengersTaken();

        $response = DB::statement("UPDATE registrodespacho SET initial_sensor_recorder = 0, final_sensor_recorder = $passengersTaken, ignore_trigger = TRUE WHERE id_registro = $dispatchRegister->id");

        if ($takings->type == RouteTaking::TAKING_BY_ALL) {
            $dispatchRegistersNoTaken = $this->getDispatchRegistersNoTaken($dispatchRegister);

            $dispatchRegistersNoTaken->each(function (DispatchRegister $drNoTaken) use ($takings, $dispatchRegister) {
                $newTakings = $drNoTaken->takings;

                $this->taking($drNoTaken, [
                    "passenger_tariff" => $newTakings->passenger_tariff,
                    "control" => 0,
                    "fuel_tariff" => $newTakings->fuel_tariff,
                    "fuel" => 0,
                    "others" => 0,
                    "bonus" => 0,
                    "advance" => 0,
                    "balance" => 0,
                    "observations" => "Turno recaudado en Vuelta: " . $dispatchRegister->round_trip . ", Ruta: " . $dispatchRegister->route->name,
                    "fuel_station_id" => $takings->fuel_station_id,
                    "counter" => $takings->counter,
                    "type" => RouteTaking::TAKING_BY_ROUND_TRIP,
                    "parent_takings_id" => $takings->id
                ]);
            });
        }

        return $response === true;
    }

    private function getDispatchRegistersNoTaken(DispatchRegister $dispatchRegister, $from = null)
    {
        $from = $from ?: $dispatchRegister->date;

        return DispatchRegister::whereCompanyAndDateRangeAndRouteIdAndVehicleId($dispatchRegister->route->company, $from, null, null, $dispatchRegister->vehicle_id)
            ->completed()
            ->where('id', '<', $dispatchRegister->id)
            ->get()

            ->filter(function (DispatchRegister $dr) use ($dispatchRegister) {
                return !$dr->takings->isTaken() // Exclude turns already taken
                    || $dr->takings->parent_takings_id == $dispatchRegister->takings->id; // Includes turn related with current takings
            });
    }
}