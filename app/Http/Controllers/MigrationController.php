<?php

namespace App\Http\Controllers;

use App\Company;
use App\ControlPoint;
use App\Route;
use App\User;
use App\Vehicle;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class MigrationController extends Controller
{
    const OLD_TABLES = [
        'companies' => 'empresa',
        'routes' => 'ruta',
        'users' => 'acceso',
        'vehicles' => 'crear_vehiculo',
        'control_points' => 'puntos_control_ruta'
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tables = collect([
            (object)[
                'name' => self::OLD_TABLES['companies'],
                'route' => route('migrate-companies'),
                'total' => DB::table(self::OLD_TABLES['companies'])->count(),
                'total_migrated' => Company::count()
            ],
            (object)[
                'name' =>self::OLD_TABLES['routes'],
                'route' => route('migrate-routes'),
                'total' => DB::table(self::OLD_TABLES['routes'])->count(),
                'total_migrated' => Route::count()
            ],
            (object)[
                'name' =>self::OLD_TABLES['users'],
                'route' => route('migrate-users'),
                'total' => DB::table(self::OLD_TABLES['users'])->count(),
                'total_migrated' => User::count()
            ],
            (object)[
                'name' =>self::OLD_TABLES['vehicles'],
                'route' => route('migrate-vehicles'),
                'total' => DB::table(self::OLD_TABLES['vehicles'])->count(),
                'total_migrated' => Vehicle::count()
            ],
            (object)[
                'name' => self::OLD_TABLES['control_points'],
                'route' => route('migrate-control-points'),
                'total' => DB::table(self::OLD_TABLES['control_points'])->count(),
                'total_migrated' => ControlPoint::count()
            ],
        ]);

        return view('home',compact('tables'));
    }


    public function migrateCompanies()
    {
        $totalCreated = 0;
        $totalUpdated = 0;
        $totalErrors = 0;
        $companies = DB::table(self::OLD_TABLES['companies'])->get();
        foreach ($companies as $companyOLD){
            $new = false;
            $company = Company::find($companyOLD->id_empresa);
            if( !$company ){
                $company = new Company();
                $new = true;
            }
            $company->id = $companyOLD->id_empresa;
            $company->name = $companyOLD->des_empresa;
            $company->shortName = $companyOLD->des_corta;
            $company->nit = $companyOLD->nit;
            $company->address = $companyOLD->direccion;
            $company->link = $companyOLD->url;
            $company->active = $companyOLD->estado;

            try{
                $company->save();
                $new?$totalCreated++:$totalUpdated++;
            }catch (QueryException $e) {
                $totalErrors++;
                dump($e);
            } catch (\PDOException $e) {
                $totalErrors++;
                dump($e);
            }
        }

        dd([
            'Total Created' => $totalCreated,
            'Total Updated' => $totalUpdated,
            'Total Errors' => $totalErrors
        ]);
    }

    public function migrateRoutes()
    {
        $totalCreated = 0;
        $totalUpdated = 0;
        $totalErrors = 0;
        $routes = DB::table(self::OLD_TABLES['routes'])->get();
        foreach ($routes as $routeOLD){
            $new = false;
            $route = Route::find($routeOLD->id_rutas);
            if( !$route ){
                $route = new Route();
                $new = true;
            }
            $route->id = $routeOLD->id_rutas;
            $route->name = $routeOLD->nombre;
            $route->distance = $routeOLD->distancia;
            $route->roadTime = $routeOLD->tiempo_recorrido;
            $route->company_id = $routeOLD->id_empresa;
            $route->dispatch_id = $routeOLD->id_despacho;
            $route->active = $routeOLD->estado==0?false:true;

            try{
                $route->save();
                $new?$totalCreated++:$totalUpdated++;
            }catch (QueryException $e) {
                $totalErrors++;
                dump($e);
            } catch (\PDOException $e) {
                $totalErrors++;
                dump($e);
            }
        }

        dd([
            'Total Created' => $totalCreated,
            'Total Updated' => $totalUpdated,
            'Total Errors' => $totalErrors
        ]);
    }

    public function migrateUsers()
    {
        $totalCreated = 0;
        $totalUpdated = 0;
        $totalErrors = 0;
        $users = DB::table(self::OLD_TABLES['users'])->get();
        foreach ($users as $userOLD){
            $new = false;
            $user = User::find($userOLD->id_usuario);
            if( !$user ){
                $user = new User();
                $new = true;
            }
            $user->id = $userOLD->id_usuario;
            $user->name = $userOLD->primer_nombre.($userOLD->primer_apellido?' '.$userOLD->primer_apellido:'');
            $user->email = $userOLD->correo;
            $user->username = $userOLD->usuario;
            $user->password = bcrypt($userOLD->clave);
            $user->role = $userOLD->nombre;
            $user->active = $userOLD->estado;
            $user->company_id = $userOLD->id_empresa;

            try{
                $user->save();
                $new?$totalCreated++:$totalUpdated++;
            }catch (QueryException $e) {
                $totalErrors++;
                dump($e);
            } catch (\PDOException $e) {
                $totalErrors++;
                dump($e);
            }
        }

        dd([
            'Total Created' => $totalCreated,
            'Total Updated' => $totalUpdated,
            'Total Errors' => $totalErrors
        ]);
    }

    public function migrateVehicles()
    {
        $totalCreated = 0;
        $totalUpdated = 0;
        $totalErrors = 0;
        $vehicles = DB::table(self::OLD_TABLES['vehicles'])->get();
        foreach ($vehicles as $vehicleOLD){
            $new = false;
            $vehicle = Vehicle::find($vehicleOLD->id_crear_vehiculo);
            if( !$vehicle ){
                $vehicle = new Vehicle();
                $new = true;
            }
            $vehicle->id = $vehicleOLD->id_crear_vehiculo;
            $vehicle->plate = $vehicleOLD->placa;
            $vehicle->number = $vehicleOLD->num_vehiculo;
            $vehicle->company_id = $vehicleOLD->empresa;
            $vehicle->active = $vehicleOLD->estado==1?true:false;
            $vehicle->in_repair = $vehicleOLD->en_taller==1?true:false;

            try{
                $vehicle->save();
                $new?$totalCreated++:$totalUpdated++;
            }catch (QueryException $e) {
                $totalErrors++;
                dump($e);
            } catch (\PDOException $e) {
                $totalErrors++;
                dump($e);
            }
        }

        dd([
            'Total Created' => $totalCreated,
            'Total Updated' => $totalUpdated,
            'Total Errors' => $totalErrors
        ]);
    }

    public function migrateControlPoints()
    {
        $totalCreated = 0;
        $totalUpdated = 0;
        $totalErrors = 0;
        $controlPoints = DB::table(self::OLD_TABLES['control_points'])->get();
        foreach ($controlPoints as $controlPointOLD){
            $new = false;
            $controlPoint = ControlPoint::find($controlPointOLD->secpuntos_control_ruta);
            if( !$controlPoint ){
                $controlPoint = new ControlPoint();
                $new = true;
            }
            $controlPoint->id = $controlPointOLD->secpuntos_control_ruta;
            $controlPoint->latitude = $controlPointOLD->lat;
            $controlPoint->longitude = $controlPointOLD->lng;
            $controlPoint->name = $controlPointOLD->nombre;
            $controlPoint->trajectory = $controlPointOLD->trayecto;
            $controlPoint->order = $controlPointOLD->orden;
            $controlPoint->type = $controlPointOLD->tipo;
            $controlPoint->distance_from_dispatch = $controlPointOLD->distancia_desde_despacho;
            $controlPoint->distance_next_point = $controlPointOLD->distancia_punto_siguiente;
            $controlPoint->route_id = $controlPointOLD->id_ruta;

            try{
                $controlPoint->save();
                $new?$totalCreated++:$totalUpdated++;
            }catch (QueryException $e) {
                $totalErrors++;
                dump($e);
            } catch (\PDOException $e) {
                $totalErrors++;
                dump($e);
            }
        }

        dd([
            'Total Created' => $totalCreated,
            'Total Updated' => $totalUpdated,
            'Total Errors' => $totalErrors
        ]);
    }

}
