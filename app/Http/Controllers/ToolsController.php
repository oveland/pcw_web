<?php

namespace App\Http\Controllers;

use App\Company;
use App\Mail\ConsolidatedReportMail;
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
        $company = Company::find(14);
        $prevDays = 1;

        $dateReport = Carbon::now()->subDay($prevDays)->toDateString();

        $mail = new ConsolidatedReportMail($company, $dateReport);
        if ($mail->buildReport()) {
            Mail::to('oscarivelan@gmail.com', $company->name)
                //->cc('soportenivel2pcwtecnologia@outlook.com')
                ->send($mail);
            dump("$company->name Mail send for date $dateReport!");
        } else {
            dump("No reports found for date $dateReport");
        }

        return view('tools.map');
    }
}
