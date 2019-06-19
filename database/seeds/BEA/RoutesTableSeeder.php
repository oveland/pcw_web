<?php

use App\Facades\BEADB;
use App\Models\Company\Company;
use App\Models\Routes\Route;
use Illuminate\Database\Seeder;

class RoutesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $routes = BEADB::select("SELECT * FROM C_RUTA");

        foreach ($routes as $routeBEA) {
            $route = Route::where('bea_id', $routeBEA->CRU_IDRUTA)->get()->first();
            if (!$route) $route = new Route();

            $route->bea_id = $routeBEA->CRU_IDRUTA;
            $route->name = $routeBEA->CRU_DESCRIPCION;
            $route->distance = 0;
            $route->road_time = 0;
            $route->url = 'none';
            $route->company_id = Company::COODETRANS;
            $route->dispatch_id = 46;
            $route->active = true;

            if (!$route->save()) {
                throw new Exception("Error saving ROUTE with id: $routeBEA->CRU_IDRUTA");
            }
        }
    }
}
