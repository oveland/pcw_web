<?php

namespace App\Http\Controllers\Reports\Users\Activity;

use App\Http\Controllers\Controller;
use App\Models\Company\Company;
use App\Models\Users\AccessLog;
use App\Services\Auth\PCWAuthService;
use App\Services\Reports\Users\ActivityLogService;
use Auth;
use Illuminate\Http\Request;
use PDF;
use Carbon\Carbon;

class UserActivityController extends Controller
{
    /**
     * @var PCWAuthService
     */
    private $auth;
    /**
     * @var ActivityLogService
     */
    private $service;

    /**
     * UserActivityController constructor.
     * @param PCWAuthService $auth
     */
    public function __construct(PCWAuthService $auth, ActivityLogService $service)
    {
        $this->middleware('super-admin');

        $this->auth = $auth;
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $access = $this->auth->access();
        $users = $this->auth->getUsersFromRequest($request);
        $users->merge(Company::find(Company::PCW)->users);
        $companies = $access->companies;
        return view('reports.users.activity.index', compact(['users', 'companies']));
    }

    public function show(Request $request)
    {
        $queryString = $request->getQueryString();
        $dateStart = $request->get('date-report');
        $withDateEnd = $request->get('with-end-date');
        $dateEnd = $withDateEnd ? $request->get('date-end-report') : null;
        $user = $request->get('user-report');

        $report = $this->service->report($dateStart, $dateEnd, $user);

        return view('reports.users.activity.show', compact(['report', 'queryString']));
    }

    public function exportLogins($date)
    {
        $logs = AccessLog::where('date', '=', Carbon::createFromFormat('Y-m-d', $date))->with('user')->orderBy('time', 'asc')->get();

        $pdf = PDF::loadView('reports.users.activity.logins', ['logs' => $logs, 'date' => $date]);
        $date = str_replace('-', '', $date);
        return $pdf->download("Reporte_Accesos_$date.pdf");
    }
}
