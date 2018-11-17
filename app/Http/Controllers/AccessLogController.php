<?php

namespace App\Http\Controllers;

use App\Models\Users\AccessLog;
use App\Models\Users\UserLog;
use PDF;
use Carbon\Carbon;

class AccessLogController extends Controller
{
    public function index()
    {
        if( \Auth::user()->isAdmin() ){
            return view('logs.index');
        }
        abort(403);
    }

    public function report($date)
    {
        if( \Auth::user()->isAdmin() ){
            $logs = AccessLog::where('date', '=', Carbon::createFromFormat('Y-m-d', $date))->with('user')->orderBy('time','asc')->get();
            
            //return view('logs.access',compact('logs'));
            $pdf = PDF::loadView('logs.access', ['logs' => $logs, 'date' => $date]);
            $date = str_replace('-','',$date);
            return $pdf->download("Reporte_Accesos_$date.pdf");
        }
        abort(403);
    }
}
