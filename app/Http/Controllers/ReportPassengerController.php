<?php

namespace App\Http\Controllers;

use App\Company;
use App\DispatchRegister;
use App\HistorySeat;
use App\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ReportPassengerController extends Controller
{
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
        if(Auth::user()->isAdmin()){
            $company = Company::find($request->get('company-report'));
        }else{
            $company = Auth::user()->company;
        }
        $dateReport = $request->get('date-report');
        $vehiclesForCompany = Vehicle::where('empresa','=',$company->id)->where('estado','=',1)->get()->pluck('placa');
        $historySeats = HistorySeat::whereIn('plate',$vehiclesForCompany)->where('date','=',$dateReport)->get()->sortBy('active_time');

        if( $request->get('export') ){
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
                'header' => [__('Passengers Report') .' '. $company->name.'. '.$dateReport],
                'data' => $data,
                'totalKm' => [__('Total KM: ').' '.$totalKm]
            ];
            $this->export($dataExport);
        }

        return view('passengers.passengersReport', compact('historySeats'));
    }

    public function export($dataExport)
    {
        $name = str_replace(' ','_',Auth::user()->company->name);
        Excel::create(__('Passengers_Report_').$name, function($excel) use($dataExport) {
            /* INFO DOCUMENT */
            $excel->setTitle(__('Passengers Report'));
            $excel->setCreator(__('PCW Ditech Integradores Tecnológicos'))->setCompany(__('PCW Ditech Integradores Tecnológicos'));
            $excel->setDescription(__('Report travel time and travel distance for vehicle seats'));

            /* FIRST SHEET */
            $excel->sheet(__('PCW Report'), function($sheet) use($dataExport) {
                $totalRows = count($dataExport->data)+3;

                $sheet->fromArray($dataExport->data);
                $sheet->prependRow($dataExport->header);
                $sheet->appendRow($dataExport->totalKm);

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
                    'F' => 'h:mm:ss'
                ));

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

                /* HEADER COLUMNS */
                $sheet->setHeight(2, 25);
                $sheet->cells('A2:G2', function($cells) {
                    $cells->setValignment('center');
                    $cells->setAlignment('center');
                    $cells->setBackground('#099585');
                    $cells->setFontColor('#eeeeee');
                    $cells->setFont(array(
                        'family'     => 'Segoe UI Light',
                        'size'       => '12',
                        'bold'       => true
                    ));
                });

                /* FOOTER */
                $sheet->setHeight($totalRows, 25);
                $sheet->mergeCells('A'.$totalRows.':G'.$totalRows);
                $sheet->cells('A'.$totalRows.':G'.$totalRows, function($cells) {
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
            });
        })->export('xlsx');
    }
}
