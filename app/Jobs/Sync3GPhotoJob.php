<?php

namespace App\Jobs;

use App\Models\Vehicles\GpsVehicle;
use App\Services\GPS\Syrus\SyrusService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class Sync3GPhotoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $gpsVehicleId;

    public function __construct($gpsVehicleId)
    {
        $this->gpsVehicleId = $gpsVehicleId;
    }

    public function handle()
    {
        $gpsVehicle = GpsVehicle::with('vehicle')->find($this->gpsVehicleId);
        if (!$gpsVehicle || !$gpsVehicle->vehicle) {
            Log::channel('sync3g')->warning("Vehículo 3G no encontrado con ID {$this->gpsVehicleId}");
            return;
        }

        try {
            $response = (new SyrusService())->syncPhoto($gpsVehicle->imei);
            $success = $response->get('success') ? 'true' : 'false';
            $message = $response->get('message');

            Log::channel('sync3g')->info(
                "✓ Vehículo {$gpsVehicle->vehicle->number} sincronizado. Success={$success} | {$message}"
            );
        } catch (\Throwable $e) {
            Log::channel('sync3g')->error(
                "✗ Error al sincronizar vehículo {$gpsVehicle->vehicle->number}: {$e->getMessage()}"
            );
        }
    }
}
