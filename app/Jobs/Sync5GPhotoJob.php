<?php

namespace App\Jobs;

use App\Services\GPS\Syrus5G\Service5GPhotoV2;
use App\Models\Vehicles\GpsVehicle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class Sync5GPhotoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $gpsVehicleId;

    /**
     *  instancia del Job.
     */
    public function __construct($gpsVehicleId)
    {
        $this->gpsVehicleId = $gpsVehicleId;
    }

    public function handle()
    {
        $gpsVehicle = GpsVehicle::find($this->gpsVehicleId);
        if (!$gpsVehicle) {
            Log::warning("Vehículo 5G no encontrado con ID {$this->gpsVehicleId}");
            return;
        }

        $service5G = new Service5GPhotoV2();

        try {
            $response = $service5G->syncPhotoV2($gpsVehicle);
            Log::info("✓ Vehículo {$gpsVehicle->vehicle->number} sincronizado correctamente: {$response}");
        } catch (\Exception $e) {
            Log::error("✗ Error al sincronizar vehículo {$gpsVehicle->vehicle->number}: " . $e->getMessage());
        }
    }
}
