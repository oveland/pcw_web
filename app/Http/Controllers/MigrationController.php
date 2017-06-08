<?php

namespace App\Http\Controllers;

use App\Company;
use App\Route;
use App\User;
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
        'users' => 'acceso'
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
                'total' => DB::table(self::OLD_TABLES['companies'])->count()
            ],
            (object)[
                'name' =>self::OLD_TABLES['routes'],
                'route' => route('migrate-routes'),
                'total' => DB::table(self::OLD_TABLES['routes'])->count()
            ],
            (object)[
                'name' =>self::OLD_TABLES['users'],
                'route' => route('migrate-users'),
                'total' => DB::table(self::OLD_TABLES['users'])->count()
            ]
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
}
