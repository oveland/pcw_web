<?php

namespace App\Http\Controllers\Reports\Liquidation;

use App;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportLiquidationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $link = config('app.beta_server_url')."/link/reportes/liquidacion/$user->id"; // TODO: Change when BETA migrated fully to NE domain

        return view('partials.iframe', compact('link'));
    }
}
