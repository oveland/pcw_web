<?php

use App\Facades\BEADB;
use App\Models\Company\Company;
use App\Models\Drivers\Driver;
use Illuminate\Database\Seeder;

class DriversTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $drivers = BEADB::select("SELECT * FROM C_CONDUCTOR");

        foreach ($drivers as $driverBEA) {
            $driver = Driver::where('bea_id', $driverBEA->CCO_IDCONDUCTOR)->get()->first();
            if (!$driver) $driver = new Driver();

            $driver->bea_id = $driverBEA->CCO_IDCONDUCTOR;
            $driver->first_name = $driverBEA->CCO_NOMBRE;
            $driver->last_name = "$driverBEA->CCO_APELLIDOP $driverBEA->CCO_APELLIDOM";
            $driver->identity = $driverBEA->CCO_CLAVECOND;
            $driver->company_id = Company::COODETRANS;
            $driver->active = true;

            if (!$driver->saveData()) {
                throw new Exception("Error saving DRIVER with id: $driverBEA->CCO_IDCONDUCTOR");
            }
        }
    }
}
