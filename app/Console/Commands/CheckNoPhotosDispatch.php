<?php

namespace App\Console\Commands;

use App\Mail\NoPhotosAlert;
use App\Models\Routes\DispatchRegister;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckNoPhotosDispatch extends Command
{
    protected $signature = 'check:no-photos-dispatch';
    protected $description = 'Verifica despachos sin fotos y envía alerta.';

    public function handle()
    {
        $company = 39; // solo expreso palmira
        $today = Carbon::now();

        //  Vehículos excluidos del chequeo
        $excludedVehicles = [

        ];

        $vehicles = Vehicle::where('company_id', $company)
            ->active()
            ->get();

        $despachosSinFotos = [];

        foreach ($vehicles as $vehicle) {

            // 🔹 Si el número está en la lista de excluidos, saltamos este vehículo
            if (in_array($vehicle->number, $excludedVehicles, true)) {
                // $this->info("Vehículo {$vehicle->number} excluido del chequeo.");
                continue;
            }

            $dispatches = DispatchRegister::where('vehicle_id', $vehicle->id)
                ->whereDate('date', $today)
                ->completed()
                ->get();

            foreach ($dispatches as $despacho) {
                if ($despacho->photos()->exists()) {
                    continue;
                }

                $despachosSinFotos[] = [
                    'id_registro'    => $despacho->id,
                    'vehicle_number' => $vehicle->number,
                    'departure_time' => $despacho->departure_time,
                    'arrival_time'   => $despacho->arrival_time,
                    'date'           => $despacho->date,
                    'routeName'      => $despacho->route->name ?? 'N/A',
                ];
            }
        }

        if (!empty($despachosSinFotos)) {
            $this->sendEmailAlert($despachosSinFotos);
            $this->info('Se encontraron despachos sin fotos. Alerta enviada.');
        } else {
            $this->info('No se encontraron despachos sin fotos.');
        }

        return 0;
    }

    protected function sendEmailAlert($despachosSinFotos)
    {
        if (!empty($despachosSinFotos)) {
            $emailTo = [
                'olmervelasquez@hotmail.com',
                'olatorre22@hotmail.com',
                'jojoavicente1@gmail.com',
            ];

            Mail::to($emailTo)->send(new NoPhotosAlert($despachosSinFotos));
        }
    }
}
