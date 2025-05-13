<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
use App\Models\Passengers\FileName;
use App\Models\Routes\DispatchRegister;
use App\Services\Exports\FileName\FileNameExport;
use Illuminate\Http\Request;

class FrameCaptureController extends Controller
{

    public function index()
    {
        $company = Company::findOrFail(39);
        $vehicles = $company->vehicles()
            ->active()
            ->whereHas('gpsVehicle.type', function ($query) {
                $query->where('type_device', '5G');
            })
            ->get();

        return view('reports.passengers.frames-photos.index', compact(['vehicles']));
    }

    public function show(Request $request)
    {
        $vehicleId = $request->get('vehicle-report');
        $date = $request->get('date-report');
        $initialDate = $date . ' 00:00:00';
        $finalDate = $date . ' 23:59:59';
        $dispatches = DispatchRegister::query()
            ->where('vehicle_id', $vehicleId)
            ->whereDateOrRange($initialDate, $finalDate)
            ->where('status', 'Terminó')
            ->with('route') // <- al final del encadenamiento
            ->get()
            ->keyBy('id');
       
        $drIds = $dispatches->keys();
        $files = FileName::whereIn('dispatch_register_id', $drIds)
            ->get()
            ->groupBy('dispatch_register_id');

        return view('reports.passengers.frames-photos.show', compact('files', 'dispatches'));
    }

    public function export(Request $request)
    {
        $vehicleId = $request->get('vehicle-report');
        $date = $request->get('date-report');
        $initialDate = $date . ' 00:00:00';
        $finalDate = $date . ' 23:59:59';

        $dispatches = DispatchRegister::query()
            ->where('vehicle_id', $vehicleId)
            ->whereDateOrRange($initialDate, $finalDate)
            ->where('status', 'Terminó')
            ->get();

        $drIds = $dispatches->pluck('id');
        $files = FileName::whereIn('dispatch_register_id', $drIds)->get();

        $exporter = new FileNameExport();
        return $exporter->export($files);
    }

}
