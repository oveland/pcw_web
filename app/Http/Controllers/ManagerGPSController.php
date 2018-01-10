<?php

namespace App\Http\Controllers;

use App\Company;
use App\Http\Controllers\API\SMS;
use App\SimGPS;
use App\Vehicle;
use Auth;
use Carbon\Carbon;
use Dompdf\Exception;
use Illuminate\Http\Request;

class ManagerGPSController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::active()->orderBy('short_name')->get();
        }
        return view('admin.gps.manage.index', compact('companies'));
    }

    public function list(Request $request)
    {
        return view('admin.gps.manage.list');
    }
}
