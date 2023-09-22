<?php

namespace App\Services\Operation\Vehicles;

use App\Models\Vehicles\Vehicle;
use App\Models\Vehicles\VehicleIssue;
use App\Models\Vehicles\VehicleIssueType;
use App\Services\Exports\PCWExporterService;
use DB;
use Throwable;

class NoveltyService
{
    /**
     * Export report to excel format
     *
     * @param $report
     */
    public function export($report)
    {
        $binnaclesByVehicles = $report->binnacles->groupBy('vehicle_id');
        $dataExcel = array();
        foreach($binnaclesByVehicles as $binnacles){
            foreach($binnacles->sortBy('date', 0, $report->sortDescending) as $binnacle){

                $vehicle = $binnacle->vehicle->number;
                $type = $binnacle->type->name;
                $notification = $binnacle->notification;

                $dataExcel[] = [
                    __('Vehicle') => $vehicle,
                    __('Fecha Vencimiento') => $binnacle->date,
                    __('Ultimo Mantenimiento') => $binnacle->prev_date,
                    __('Type') =>$type,
                    __('Descripción') => $binnacle->observations,
                    __('KM Recorrido') => $binnacle->getMileageTraveled(),
                    __('KM Vencimiento') => $binnacle->mileage_expiration,
                    __('User create') =>$binnacle->user->name,
                ];
            }
        }

       /* $vehicleIssuesGroups = $report->vehicleIssues->groupBy('issue_uid');

        $dataExcel = array();
        foreach ($vehicleIssuesGroups as $issueUid => $vehicleIssuesGroup) {
            $issueIn = VehicleIssue::where('issue_uid', $issueUid)->where('issue_type_id', VehicleIssueType::IN)->get()->first();
            $dateIn = $issueIn ? $issueIn->date : null;

            foreach ($vehicleIssuesGroup->sortBy('date') as $issue) {
                $vehicle = $issue->vehicle;
                $type = $issue->type;
                $user = $issue->user;
                $driver = $issue->driver;

                $duration = $type->id == VehicleIssueType::OUT ? ($dateIn ? $issue->date->diffAsCarbonInterval($dateIn, false)->forHumans() : __('Greater than one day')) : null;

                $dataExcel[] = [
                    __('Vehicle') => $vehicle->number,                      # A CELL
                    __('Date') => $issue->date->toDateTimeString(),          # B CELL
                    __('Type') => $type->name . ($duration ? "\n$duration" : ""),                              # C CELL
                    __('Vehicle issue') => $issue->observations,            # D CELL
                    __('Driver') => $driver ? $driver->fullName() : "",     # E CELL
                    __('User') => $user->name,                              # F CELL
                ];
            }
        }*/



        PCWExporterService::excel([
            'fileName' => __('Vehicle issues') . " " . __('Vehicles') . " $report->dateReport",
            'title' => __('Vehicle issues') . " " . __('Vehicles') . " $report->dateReport",
            'subTitle' => __('Vehicle issues') . " " . __('Vehicles'),
            'data' => $dataExcel
        ]);
    }

    /**
     * @param Vehicle $vehicle
     * @param $issueTypeId
     * @param $observations
     * @param bool $forceOut
     * @param bool $setInRepair
     * @param $date
     * @param null $userId
     * @return mixed
     * @throws Throwable
     */
    function create(Vehicle $vehicle, $issueTypeId, $observations, $forceOut = false, $setInRepair = true, $date = null, $userId = null)
    {
        return DB::transaction(function () use ($vehicle, $issueTypeId, $observations, $forceOut, $setInRepair, $date, $userId) {
            $success = false;
            $message = "";

            $currentIssue = $vehicle->getCurrentIssue();
            if ($date) $currentIssue->date = explode('.', $date)[0];
            if ($userId) $currentIssue->user_id = $userId;

            if (!$vehicle->active || !$forceOut && ($issueTypeId == VehicleIssueType::OUT || $issueTypeId == VehicleIssueType::IN) && $vehicle->in_repair) {
                if ($issueTypeId != VehicleIssueType::IN) {
                    if ($vehicle->in_repair && !$forceOut && $setInRepair) {
                        $observations .= ". " . __('Continue in repair');
                    }
                    if (!$vehicle->active) {
                        $observations .= ". " . __('Continue inactive');
                    }
                }
                $issueTypeId = VehicleIssueType::UPDATE;
            }

            if ($currentIssue->readyForIn() && $issueTypeId != VehicleIssueType::OUT) {
                $issueTypeId = VehicleIssueType::IN;
                if ($setInRepair) $observations = __('Set in repair') . ". $observations";
            }


            $currentIssue->issue_type_id = $issueTypeId;
            $currentIssue->generateUid();

            $currentIssue->observations = $observations;

            $issue = new VehicleIssue($currentIssue->toArray());

            if ($currentIssue->save() && $issue->save()) {
                try {
                    $quitIssue = $currentIssue->issue_type_id == VehicleIssueType::OUT;
                    if ($quitIssue || ($forceOut && !$setInRepair)) {
                        DB::statement("UPDATE crear_vehiculo SET en_taller = 0 WHERE id_crear_vehiculo = $vehicle->id");
                        DB::statement("UPDATE vehicles SET in_repair = FALSE WHERE id = $vehicle->id");
                    } else if ($setInRepair) {
                        DB::statement("UPDATE crear_vehiculo SET en_taller = 1, observaciones = '$currentIssue->observations' WHERE id_crear_vehiculo = $vehicle->id");
                        DB::statement("UPDATE vehicles SET in_repair = TRUE, observations = '$currentIssue->observations' WHERE id = $vehicle->id");
                    }
                } catch (\Exception $e) {
                    dump('Error updating vehicle in repair', $e);
                }

                $success = true;
                $message = __('Issue registered successfully') . ". ";
            } else {
                if (!$currentIssue->save()) $message .= __('Error in registering issue') . ". ";
                if (!$currentIssue->save()) $message .= __('Error in registering Current issue') . ". ";
            }

            return (object)[
                'success' => $success,
                'message' => $message,
                'currentIssueId' => $currentIssue->id,
                'issue' => $issue->id,
            ];
        });
    }
}