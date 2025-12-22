<?php

namespace App\Console\Commands\sync5G;

use App\Services\GPS\Syrus5G\Service5GPhotoV2;
use Illuminate\Console\Command;
use App\Models\Company\Company;
use App\Models\Vehicles\GpsVehicle;
use App\Models\Vehicles\Vehicle;
use App\Jobs\Sync5GPhotoJob;

class Sync5GPhotoCommandV2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync5GV2:sync-photos {--vh=} {--empresa=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza fotos de vehículos 5G por empresa';

    /**
     * @var Service5GPhotoV2
     */
    private $service5GPhotoV2;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->service5GPhotoV2 = new Service5GPhotoV2();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $empresaId = $this->option('empresa');
        $vehicleId = $this->option('vh');

        if ($vehicleId) {
            $this->syncSingleVehicle($vehicleId);
            return 0;
        }

        if ($empresaId) {
            $this->syncVehiclesByCompany($empresaId);
            return 0;
        }

        $this->error('Debe especificar --empresa o --vh');
        return 1;
    }

    /**
     * Sincroniza un vehículo específico
     */
    private function syncSingleVehicle($vehicleId)
    {
        $gpsVehicle = GpsVehicle::where('vehicle_id', $vehicleId)
            ->where('technology', '5G')
            ->first();

        if (!$gpsVehicle) {
            $this->error("No se encontró vehículo 5G con ID: {$vehicleId}");
            return;
        }

        $this->info("Sincronizando vehículo ID: {$vehicleId}, IMEI: {$gpsVehicle->imei}");

        try {
            $response = $this->service5GPhotoV2->syncPhotoV2($gpsVehicle);
            $this->info("✓ Respuesta: {$response}");
        } catch (\Exception $e) {
            $this->error("✗ Error: " . $e->getMessage());
        }
    }

    /**
     * Sincroniza todos los vehículos 5G de una empresa
     */
    private function syncVehiclesByCompany($empresaId)
    {
        $company = Company::find($empresaId);

        if (!$company) {
            $this->error("No se encontró la empresa con ID: {$empresaId}");
            return;
        }

        $this->info("Sincronizando vehículos 5G de la empresa: {$company->name}");

        $gpsVehicles = GpsVehicle::whereHas('vehicle', function($query) use ($empresaId) {
            $query->where('company_id', $empresaId)->where('active', true);
        })
            ->where('technology', '5G')
            ->with('vehicle')
            ->get();

        if ($gpsVehicles->isEmpty()) {
            $this->info("No se encontraron vehículos 5G activos para la empresa: {$company->name}");
            return;
        }

        $this->info("Se enviarán {$gpsVehicles->count()} vehículos a la cola de sincronización...");

        foreach ($gpsVehicles as $gpsVehicle) {
            Sync5GPhotoJob::dispatch($gpsVehicle->id)->onQueue('sync5g');
            $this->info("→ Vehículo {$gpsVehicle->vehicle->number} enviado a la cola.");
        }

        $this->info("Todos los vehículos fueron enviados a la cola.");
    }
}