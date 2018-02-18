<?php

namespace App\Http\Controllers;

use App\Company;
use Auth;
use Illuminate\Http\Request;

class StatusCounterController extends Controller
{
    public function index(Request $request)
    {

        dd("Index Status Counter");
        if (Auth::user()->isAdmin()) {
            $companies = Company::active()->orderBy('short_name')->get();
        }
        return view('admin.gps.manage.index', compact('companies'));
    }

    public function list(Request $request)
    {
        dd('List Status Counter');
    }
}
