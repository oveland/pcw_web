<?php

namespace App\Console\Commands\Sync3G;

use App\Jobs\Sync3GPhotoJob;
use App\Models\Company\Company;
use App\Models\Vehicles\GpsVehicle;
use Illuminate\Console\Command;

class Sync3GPhotoCommandV2 extends Command
{
    protected $signature = 'sync3GV2:sync-photos {--vh=} {--empresa=}';

    protected $description = 'Encola la sincronización de fotos de vehículos 3G';

    public function handle()
    {
        $vehicleId = $this->option('vh');
        $companyId = $this->option('empresa');

        if ($vehicleId) {
            return $this->dispatchSingleVehicle($vehicleId);
        }

        if ($companyId) {
            return $this->dispatchCompanyVehicles($companyId);
        }

        return $this->dispatchAllVehicles();
    }

    private function dispatchSingleVehicle($vehicleId)
    {
        $gpsVehicle = $this->baseQuery()
            ->where('vehicle_id', $vehicleId)
            ->first();

        if (!$gpsVehicle) {
            $this->error("No se encontró vehículo 3G activo con ID: {$vehicleId}");
            return 1;
        }

        Sync3GPhotoJob::dispatch($gpsVehicle->id)->onQueue('syrus3g');
        $this->info("Vehículo {$gpsVehicle->vehicle->number} enviado a la cola syrus3g.");

        return 0;
    }

    private function dispatchCompanyVehicles($companyId)
    {
        $company = Company::find($companyId);
        if (!$company) {
            $this->error("No se encontró la empresa con ID: {$companyId}");
            return 1;
        }

        $gpsVehicles = $this->baseQuery()
            ->whereHas('vehicle', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->get();

        return $this->dispatchVehicles($gpsVehicles, "empresa {$company->name}");
    }

    private function dispatchAllVehicles()
    {
        $gpsVehicles = $this->baseQuery()->get();

        return $this->dispatchVehicles($gpsVehicles, 'todos los vehículos 3G activos');
    }

    private function dispatchVehicles($gpsVehicles, $label)
    {
        if ($gpsVehicles->isEmpty()) {
            $this->info("No se encontraron vehículos para {$label}.");
            return 0;
        }

        $this->info("Se enviarán {$gpsVehicles->count()} vehículos a la cola syrus3g para {$label}.");

        foreach ($gpsVehicles as $gpsVehicle) {
            Sync3GPhotoJob::dispatch($gpsVehicle->id)->onQueue('syrus3g');
            $this->line("→ Vehículo {$gpsVehicle->vehicle->number} enviado.");
        }

        return 0;
    }

    private function baseQuery()
    {
        return GpsVehicle::with('vehicle')
            ->where('technology', '3G')
            ->whereHas('vehicle', function ($query) {
                $query->where('active', true);
            });
    }
}
