<?php

namespace App\Http\Controllers;

use App\Company;
use App\MaintenanceVehicle;
use App\Services\PCWTime;
use App\Vehicle;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class MaintenanceVehicleController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->isAdmin()) abort(403);
        $companies = Company::active()->orderBy('short_name')->get();
        return view('admin.vehicles.maintenance.index', compact('companies'));
    }

    public function show(Request $request)
    {
        $company = Auth::user()->isAdmin() ? Company::find($request->get('company-report')) : Auth::user()->company;
        $vehicles = $company->vehicles->where('active', true)->sortBy(function ($vehicle, $key) {
            return substr($vehicle->plate, -1); // Sort vehicle by last number asc
        });

        $maintenanceVehiclesReports = collect([]);
        foreach ($vehicles as $vehicle) {
            $maintenanceReport = $vehicle->maintenance;
            $maintenanceVehiclesReports->push((object)[
                'vehicleId' => $vehicle->id,
                'vehicleNumber' => $vehicle->number,
                'vehiclePlate' => $vehicle->plate,
                'hasMaintenanceReports' => $maintenanceReport->count() > 0,
                'maintenanceReport' => $maintenanceReport
            ]);
        }

        return view('admin.vehicles.maintenance.show', compact('maintenanceVehiclesReports'));
    }

    public function create(Vehicle $vehicle, Request $request)
    {
        $date = Carbon::createFromFormat('Y-m-d', $request->get('date'));
        return self::registerMaintenance($vehicle,$date);
    }

    public function update(MaintenanceVehicle $maintenanceVehicle, Request $request)
    {
        $vehicle = $maintenanceVehicle->vehicle;
        $date = Carbon::createFromFormat('Y-m-d', $request->get('date'));
        return self::registerMaintenance($vehicle,$date);
    }

    private static function registerMaintenance($vehicle,$date)
    {
        DB::delete("DELETE FROM maintenance_vehicles WHERE vehicle_id = $vehicle->id");

        $dateRange = PCWTime::dateRange($date, $date->copy()->addMonth(6));

        $periodDays = config('vehicle.maintenance_period_days');
        $checkAssignable = config('vehicle.maintenance_check_assignable_days');
        $totalRegisters = 0;
        foreach ($dateRange as $nextDate) {
            if ($checkAssignable && ($nextDate->dayOfWeek == Carbon::SUNDAY || $nextDate->dayOfWeek == Carbon::MONDAY)){
                $assignable = false;
            }
            else{
                $assignable = true;
            }
            $periodDays++;

            if ($periodDays > config('vehicle.maintenance_period_days') && $assignable) {
                $maintenanceVehicle = new MaintenanceVehicle();
                $maintenanceVehicle->vehicle_id = $vehicle->id;
                $maintenanceVehicle->date = $nextDate->toDateString();
                $maintenanceVehicle->week_day = $nextDate->dayOfWeek;
                $maintenanceVehicle->observations = config('vehicle.maintenance_default_observations');
                if ($maintenanceVehicle->save()) $totalRegisters++;
                $periodDays = 1;
            }
        }

        return (string)$totalRegisters;
    }

    public function delete(Company $company)
    {
        if (Auth::user()->isAdmin() || Auth::user()->belongsToCompany($company)) {
            return (string)DB::delete("DELETE FROM maintenance_vehicles WHERE vehicle_id IN (SELECT id FROM vehicles WHERE company_id = $company->id)");
        }
        return 'error';
    }
}
