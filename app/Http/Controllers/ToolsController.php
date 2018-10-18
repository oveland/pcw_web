<?php

namespace App\Http\Controllers;

use App\Company;
use App\Mail\ConsolidatedReportMail;
use App\Report;
use App\Services\pcwserviciosgps\ConsolidatedReportsService;
use Carbon\Carbon;
use Mail;
use Illuminate\Http\Request;

class ToolsController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function map(Request $request)
    {
        return view('tools.map');
    }
}
