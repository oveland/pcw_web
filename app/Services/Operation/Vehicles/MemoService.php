<?php

namespace App\Services\Operation\Vehicles;

use App\Models\Company\Company;
use App\Models\Vehicles\Memos\Memo;
use App\Models\Vehicles\Vehicle;
use App\Services\PCWExporterService;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class MemoService
{
    /**
     * @param Memo | null $memo
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function process(Memo $memo = null, Request $request)
    {
        if (!$memo) {
            $memo = new Memo();
            $action = 'created';
            $memo->createdUser()->associate(Auth::user());
        } else {
            $action = 'updated';
            $memo->editedUser()->associate(Auth::user());
        }

        $response = collect([
            'success' => true,
            'message' => __("Memo register $action successfully")
        ]);

        $vehicle = Vehicle::find($request->get('vehicle'));
        $memo->vehicle()->associate($vehicle);
        $memo->date = $request->get('date');
        $memo->observations = $request->get('observations');

        if (!$memo->save()) {
            $response->put('success', false);
            $response->put('message', __("Memo register not $action"));
        }

        return response()->json($response);
    }

    public function report(Company $company, $vehicleReport, $dateReport, $withEndDate, $dateEndReport, $sortDescending = false)
    {
        $vehicles = ($vehicleReport == 'all') ? $company->vehicles : $company->vehicles()->where('id', $vehicleReport)->get();

        $memos = Memo::whereIn('vehicle_id', $vehicles->pluck('id'))->where(function ($query) use ($dateReport, $dateEndReport) {
            $query->whereBetween('date', ["$dateReport 00:00:00", "$dateEndReport 23:59:59"]);
        });

        $memos = $memos->get();

        return (object)[
            'company' => $company,
            'vehicleReport' => $vehicleReport,
            'dateReport' => $dateReport,
            'withEndDate' => $withEndDate,
            'dateEndReport' => $dateEndReport,
            'memos' => $memos->sortBy('date', 0, $sortDescending)->sortBy('id'),
            'isNotEmpty' => $memos->isNotEmpty(),
            'sortDescending' => $sortDescending,
        ];
    }

    /**
     * Export report to excel format
     *
     * @param $report
     */
    public function export($report) // TODO: Implements export
    {
        $vehicleMemos = $report->memos->groupBy('vehicle_id');

        $dataExcel = array();
        foreach ($vehicleMemos as $memos) {
            foreach ($memos->sortBy('date') as $memo) {
                $vehicle = $memo->vehicle;
                $date = $memo->date;
                $observations = $memo->observations;
                $createdUser = $memo->createdUser;
                $editedUser = $memo->editedUser;

                $dataExcel[] = [
                    __('Vehicle') => $vehicle->number,                      # A CELL
                    __('Date') => $date->toDateString(),                    # B CELL
                    __('Observations') => $observations,                    # C CELL
                    __('Created user') => $createdUser->name,               # D CELL
                    __('Edited user') => $editedUser->name,                 # E CELL
                ];
            }
        }

        PCWExporterService::excel([
            'fileName' => __('Vehicle') . " " . __('memos') . " $report->dateReport",
            'title' => __('Vehicle') . " " . __('memos') . " $report->dateReport",
            'subTitle' => __('Vehicle') . " " . __('memos'),
            'data' => $dataExcel
        ]);
    }
}
