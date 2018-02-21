<?php

namespace App\Http\Controllers;

use App\Company;
use App\CounterIssue;
use Auth;
use DB;
use Illuminate\Http\Request;

class StatusCounterController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::active()->orderBy('short_name')->get();
        }
        return view('admin.counter.status.index', compact('companies'));
    }

    public function list(Request $request)
    {
        $companyReport = $request->get('company-report');
        $typeReport = $request->get('type-report');
        $initialDate = $request->get('initial-date');
        $finalDate = $request->get('final-date');
        if( $initialDate > $finalDate )return view('partials.dates.invalidRange');

        switch ($typeReport){
            case 'issues':
                $company = Auth::user()->isAdmin() ? Company::find($companyReport) : Auth::user()->company;
                $vehicles = $company->vehicles;

                $counterIssues = CounterIssue::whereIn('vehicle_id', $vehicles->pluck('id'))
                    ->whereBetween('date', [$initialDate, $finalDate])
                    ->orderBy('id')
                    ->get()
                    ->take(10);

                return view('admin.counter.status.list', compact('counterIssues'));
                break;
            case 'history':
                return view('partials.alerts.featureOnDevelopment');
                break;
            default:
                return 'NONE';
                break;
        }
    }
}
