<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
use App\Models\Drivers\Driver;
use App\Http\Controllers\Utils\CSV;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DriverController extends Controller
{
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::active()->orderBy('short_name')->get();
        }
        return view('admin.drivers.index', compact('companies'));
    }

    public function csv(Request $request)
    {
        $companyReport = $request->get('company-report');
        $company = Auth::user()->isAdmin() ? Company::find($companyReport) : Auth::user()->company;


        $fileDrivers = $request->file('csv-drivers');
        $drivers = CSV::toArray($fileDrivers->getRealPath(), ';');

        $driversUpdated = 0;
        $driversCreated = 0;

        foreach ($drivers as $driver) {
            $driver = (object)$driver;
            foreach ($driver as $key => &$value) {
                $value = utf8_encode($value);
            }
            $query = "
                UPDATE conductor SET
                nombre1 = '$driver->nombre1',
                nombre2 = '$driver->nombre2',
                apellido1 = '$driver->apellido1',
                apellido2 = '$driver->apellido2',
                codigo_interno = '$driver->codigo_interno',
                telefono = '$driver->telefono',
                celular = '$driver->celular'
                WHERE identidad = '$driver->identidad'
            ";
            $updated = DB::update($query);
            if (!$updated) {
                $driverDB = DB::select("SELECT * FROM conductor WHERE identidad = '$driver->identidad'");

                if (count($driverDB)) {
                    dump("Error on: $query");
                } else {
                    $query = "INSERT INTO conductor (identidad,nombre1,nombre2,apellido1,apellido2,codigo_interno,telefono,celular) VALUES ('$driver->identidad','$driver->nombre1','$driver->nombre2','$driver->apellido1','$driver->apellido2','$driver->codigo_interno','$driver->telefono','$driver->celular')";
                    $insert = DB::insert($query);
                    if ($insert) {
                        dump("CREATED DATA: $query");
                        $driversCreated++;
                    } else {
                        dump("ERROR inserting data: $query");
                    }
                }
            }else{
                $driversUpdated++;
            }
        }
        dump([
            'created' => $driversUpdated,
            'updated' => $driversCreated
        ]);
        dump("-------------------  CSV DATA --------------------------");
        dd($drivers);
    }
}
