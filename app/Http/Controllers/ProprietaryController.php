<?php

namespace App\Http\Controllers;

use App\Company;
use App\Driver;
use App\Proprietary;
use Auth;
use Illuminate\Http\Request;

class ProprietaryController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::active()->orderBy('short_name')->get();
        }
        return view('admin.proprietaries.index', compact('companies'));
    }

    public function show(Request $request)
    {
        $company = GeneralController::getCompany($request);
        $vehicles = $company->vehicles;
        $proprietaries = $company->proprietaries->sortBy('surname');

        return view('admin.proprietaries.list', compact(['proprietaries','vehicles']));
    }
}
