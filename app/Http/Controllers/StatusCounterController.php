<?php

namespace App\Http\Controllers;

use App\Company;
use App\CounterIssue;
use App\Passenger;
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

        $company = Auth::user()->isAdmin() ? Company::find($companyReport) : Auth::user()->company;
        $vehicles = $company->vehicles;

        switch ($typeReport){
            case 'issues':
                $counterIssues = CounterIssue::whereIn('vehicle_id', $vehicles->pluck('id'))
                    ->whereBetween('date', [$initialDate, $finalDate])
                    ->orderBy('id')
                    ->get();

                return view('admin.counter.status.listIssues', compact('counterIssues'));
                break;
            case 'history':
                $passengers = Passenger::whereIn('vehicle_id', $vehicles->pluck('id'))
                    ->whereBetween('date', [$initialDate, $finalDate])
                    ->orderBy('date')
                    ->get();

                return view('admin.counter.status.listHistory', compact('passengers'));
                break;
            default:
                return 'NONE';
                break;
        }
    }
}
