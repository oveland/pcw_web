<?php

namespace App\Http\Controllers;

use App\Company;
use App\DispatchRegister;
use App\HistorySeat;
use App\Report;
use App\Route;
use App\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class PassengerReportController extends Controller
{
    const DISPATCH_COMPLETE = 'Terminó';
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if( Auth::user()->isAdmin() ){
            $companies = Company::where('active', '=', true)->orderBy('shortName','asc')->get();
        }
        return view('passengers.index', compact('companies'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        $dateReport = $request->get('date-report');
        $routeId = $request->get('route-report');
        $company = Auth::user()->isAdmin()?Company::find($request->get('company-report')):Auth::user()->company;
        $vehiclesForCompany = $company->activeVehicles->pluck('plate');

        $dispatchRegister = null;
        $location_dispatch = null;

        if($routeId!='all'){
            $roundTripDispatchRegisters = DispatchRegister::where('date', '=', $dateReport)
                ->where('route_id', '=', $routeId)->where('status','=',self::DISPATCH_COMPLETE)
                ->orderBy('round_trip','asc')->get()->groupBy('round_trip');
            return view('passengers.passengersReportByRoute', compact('roundTripDispatchRegisters'));
        }
        //$historySeats = $historySeats->whereBetween('active_time',[$dateReport.' '.$dispatchRegister->departure_time,$dateReport.' '.$dispatchRegister->arrival_time_scheduled]);

        $historySeats = HistorySeat::whereIn('plate',$vehiclesForCompany)
            ->where('date','=',$dateReport)
            ->get()->sortBy('active_time');

        if( $request->get('export') ) $this->export($historySeats,$company,$dateReport,null);
        return view('passengers.passengersReportByAll', compact('historySeats'));
    }

    public function showByDispatch(DispatchRegister $dispatchRegister, Request $request){
        $dispatchArrivaltime = $dispatchRegister->arrival_time_scheduled;

        if($dispatchArrivaltime > "23:59:59")$dispatchArrivaltime= "23:59:59";
        $historySeats = HistorySeat::where('plate',$dispatchRegister->vehicle->plate)
            ->where('date','=',$dispatchRegister->date)
            ->whereBetween('active_time',
                [
                    $dispatchRegister->date.' '.$dispatchRegister->departure_time,
                    $dispatchRegister->date.' '.($dispatchRegister->canceled?$dispatchRegister->time_canceled:$dispatchArrivaltime)
                ])
            ->get()->sortBy('active_time');

        if( $request->get('export') ) $this->export($historySeats,$dispatchRegister->route->company,$dispatchRegister->date, $dispatchRegister);

        return view('passengers.passengersReport', compact(['historySeats','dispatchRegister']));
    }

    public function export($historySeats,$company,$dateReport,$dispatchRegister)
    {
        $data = [];
        $totalKm = 0;
        $number = 1;
        foreach ($historySeats as $historySeat){
            $km = $historySeat->busy_km / 1000;
            if($historySeat->inactive_time)$totalKm += $km;
            $data[] = [
                'N°' => $number++,
                __('Vehicle') => $historySeat->plate,
                __('Seat') => $historySeat->seat,
                __('Event active time') => $historySeat->active_time?date('H:i:s',strtotime(explode(" ",$historySeat->active_time)[1])):__('Still busy'),
                __('Event inactive time') => $historySeat->inactive_time?date('H:i:s',strtotime(explode(" ",$historySeat->inactive_time)[1])):__('Still busy'),
                __('Active time') => $historySeat->inactive_time?date('H:i:s',strtotime($historySeat->busy_time)):__('Still busy'),
                __('Active kilometers') => $historySeat->inactive_time?$km:__('Still busy'),
            ];
        }

        $dataExport = (object)[
            'fileName' => __('Passengers_Report_'). str_replace(' ','_',$company->name).'.'.str_replace('-','',$dateReport),
            'header' => [__('Passengers Report') .' '. $company->name.'. '.($dispatchRegister?$dispatchRegister->route->name:'').'. '.$dateReport],
            'data' => $data,
            'totalKm' => [__('Total KM: ').' '.number_format($totalKm,2,',','.')]
        ];

        Excel::create($dataExport->fileName, function($excel) use($dataExport) {
            /* INFO DOCUMENT */
            $excel->setTitle(__('Passengers Report'));
            $excel->setCreator(__('PCW Ditech Integradores Tecnológicos'))->setCompany(__('PCW Ditech Integradores Tecnológicos'));
            $excel->setDescription(__('Report travel time and travel distance for vehicle seats'));

            /* FIRST SHEET */
            $excel->sheet(__('PCW Report'), function($sheet) use($dataExport) {
                $totalRows = count($dataExport->data)+3;

                $sheet->fromArray($dataExport->data);
                $sheet->prependRow($dataExport->totalKm);
                $sheet->prependRow($dataExport->header);

                /* GENEREAL STYLE */
                $sheet->setOrientation('landscape');
                $sheet->setFontFamily('Segoe UI Light');
                $sheet->setBorder('A1:G'.$totalRows, 'thin');
                $sheet->cells('A1:G'.$totalRows, function($cells) {
                    $cells->setFontFamily('Segoe UI Light');
                });

                /* COLUMNS FORMAT */
                $sheet->setColumnFormat(array(
                    'D' => 'h:mm:ss',
                    'E' => 'h:mm:ss',
                    'F' => 'h:mm:ss',
                    'G' => '#,##0.00'
                ));
                $sheet->setAutoFilter('A3:G'.($totalRows));

                /*  HEADER */
                $sheet->setHeight(1, 50);
                $sheet->mergeCells('A1:G1');
                $sheet->cells('A1:G1', function($cells) {
                    $cells->setValignment('center');
                    $cells->setAlignment('center');
                    $cells->setBackground('#0e6d62');
                    $cells->setFontColor('#eeeeee');
                    $cells->setFont(array(
                        'family'     => 'Segoe UI Light',
                        'size'       => '14',
                        'bold'       => true
                    ));
                });

                /* HEADER TOTAL */
                $sheet->setHeight(2, 25);
                $sheet->mergeCells('A2:G2');
                $sheet->cells('A2:G2', function($cells) {
                    $cells->setValignment('center');
                    $cells->setAlignment('right');
                    $cells->setBackground('#0d4841');
                    $cells->setFontColor('#eeeeee');
                    $cells->setFont(array(
                        'family'     => 'Segoe UI Light',
                        'size'       => '12',
                        'bold'       => true
                    ));
                });

                /* HEADER COLUMNS */
                $sheet->setHeight(3, 40);
                $sheet->cells('A3:G3', function($cells) {
                    $cells->setValignment('center');
                    $cells->setAlignment('center');
                    $cells->setBackground('#0d4841');
                    $cells->setFontColor('#eeeeee');
                    $cells->setFont(array(
                        'family'     => 'Segoe UI Light',
                        'size'       => '12',
                        'bold'       => true
                    ));
                });
            });
        })->export('xlsx');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function ajax(Request $request)
    {
        switch ($request->get('option')){
            case 'loadRoutes':
                if(Auth::user()->isAdmin()){
                    $company = $request->get('company');
                }else{
                    $company = Auth::user()->company->id;
                }
                $routes = $company!='null'?Route::where('company_id', '=', $company)->orderBy('name','asc')->get():[];
                return view('passengers.routeSelect', compact('routes'));
                break;
            default:
                return "Nothing to do";
                break;
        }
    }
}
