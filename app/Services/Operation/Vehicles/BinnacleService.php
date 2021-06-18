<?php

namespace App\Services\Operation\Vehicles;

use App\LastLocation;
use App\Mail\Vehicles\Binnacles\NotificationMail;
use App\Models\Company\Company;
use App\Models\Routes\DispatchRegister;
use App\Models\Users\User;
use App\Models\Vehicles\Binnacles\Binnacle;
use App\Models\Vehicles\Binnacles\Notification;
use App\Models\Vehicles\Binnacles\NotificationUser;
use App\Models\Vehicles\Binnacles\Type;
use App\Models\Vehicles\Vehicle;
use App\Models\Vehicles\VehicleIssue;
use App\Models\Vehicles\VehicleIssueType;
use App\Services\PCWExporterService;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class BinnacleService
{
    /**
     * @param Binnacle | null $binnacle
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function process(Binnacle $binnacle = null, Request $request)
    {
        $action = 'updated';
        $update = true;

        $vehicle = Vehicle::find($request->get('vehicle'));
        $type = Type::find($request->get('type'));

        DB::beginTransaction();

        $currentLocation = $vehicle->currentLocation;

        if (!$binnacle) {
            $binnacle = new Binnacle();

            $binnacle->mileage_odometer = $currentLocation->odometer;
            $binnacle->mileage_route = $currentLocation->mileage_route;

            $action = 'created';
            $update = false;
        }


        $prevDate = $request->get('prev-date');
        $lastLocation = null;
        if ($prevDate) {
            $lastLocation = LastLocation::whereDate('date', '<', $prevDate)
                ->where('vehicle_id', $vehicle->id)
                ->orderByDesc('date')
                ->first();
        }
        if ($lastLocation) {
            $binnacle->mileage_odometer = $lastLocation->odometer;

//            if ($prevDate > '2021-06-15') {
//                $binnacle->mileage_route = $lastLocation->mileage_route;
//            } else {
//                $drs = DispatchRegister::active()
//                    ->where('vehicle_id', $vehicle->id)
//                    ->whereBetween('date', [$prevDate, Carbon::now()])->get();
//
//                $routeKm = $drs->sum(function (DispatchRegister $dr) {
//                    return $dr->route->distance_in_meters;
//                });
//
//                $binnacle->mileage_route = $currentLocation->mileage_route - $routeKm;
//            }

            $drs = DispatchRegister::active()
                ->where('vehicle_id', $vehicle->id)
                ->whereBetween('date', [$prevDate, Carbon::now()])->get();

            $routeKm = $drs->sum(function (DispatchRegister $dr) {
                $lastControlPoint = $dr->route->controlPoints()->get()->sortBy('order')->last();
                return $lastControlPoint ? $lastControlPoint->distance_from_dispatch : 0;
            });

            $binnacle->mileage_route = $currentLocation->mileage_route - $routeKm;
        }


        $response = collect([
            'success' => true,
            'message' => __("Binnacle register $action successfully")
        ]);

        $binnacle = $binnacle->fill([
            'date' => $request->get('date'),
            'prev_date' => $request->get('prev-date'),
            'mileage' => $request->get('mileage'),
            'observations' => $request->get('observations')
        ]);

        $binnacle->vehicle()->associate($vehicle);
        $binnacle->user()->associate(Auth::user());
        $binnacle->type()->associate($type);


        if (!$binnacle->save()) {
            $response->put('success', false);
            $response->put('message', __("Binnacle register not $action"));
        } else {
            $notificationData = [
                'date' => $request->get('notification-date'),
                'mileage' => $request->get('notification-mileage'),
                'period' => $request->get('notification-period'),
                'day_of_month' => $request->get('day_of_month'),
                'day_of_week' => $request->get('day_of_week')
            ];

            $notification = $update ? $binnacle->notification->fill($notificationData) : new Notification($notificationData);

            if (!$binnacle->notification()->save($notification)) {
                $response->put('success', false);
                $response->put('message', __("Binnacle notification not $action"));
            } else {
                $notification = $binnacle->refresh()->notification;
                $oldUsers = $notification->notificationUsers->pluck('user.id');
                $newUsers = collect($request->get('notification-users'));

                // Delete excluded notification users
                $notification->notificationUsers->filter(function (NotificationUser $notificationUser) use ($newUsers) {
                    return !$newUsers->contains($notificationUser->user->id);
                })->each(function (NotificationUser $notificationUser) {
                    return $notificationUser->delete();
                });

                $newUsers = $newUsers->diff($oldUsers);


                foreach ($newUsers as $newUser) {
                    $user = User::find($newUser);
                    if ($user) {
                        $notification->notificationUsers()->create([
                            'user_id' => $user->id,
                        ]);
                    }
                }
            }
        }

        if ($response->get('success')) {
            DB::commit();
        }

        return response()->json($response);
    }

    /**
     * @param Company|null $company
     * @return \Illuminate\Support\Collection
     */
    public function notificationsByUsers(Company $company = null)
    {
        $data = collect([]);
        $report = $this->currentNotifications($company);

        $notificationUsers = $report->pluck('notificationUsers')->collapse();
        $users = $notificationUsers->pluck('user', 'user_id');

        foreach ($users as $user) {
            $notificationsUser = $notificationUsers->where('user_id', $user->id);

            $notificationsBinnacles = $notificationsUser->pluck('notification');

            $userBinnacles = $notificationsBinnacles->pluck('binnacle', 'binnacle_id');

            $data->push((object)[
                'user' => $user,
                'binnacles' => $userBinnacles,
                'notifications' => $notificationsUser
            ]);
        }

        return $data;
    }

    /**
     * @param Company|null $company
     * @return Notification[]|Builder[]|Collection
     */
    public function currentNotifications(Company $company = null)
    {
        $notifications = Notification::with(['binnacle', 'notificationUsers'])->where(function ($query) {
            $query->whereDate('date', Carbon::now())->orWhere('mileage', '>', 0);
        })->get();

        $notifications = $notifications->filter(function (Notification $n) use ($company) {
            $binnacle = $n->binnacle;

            $companyNotifications = $company ? $company->vehicles->contains($binnacle->vehicle->id) : true;
            $notificationsByMileage = $binnacle->mileage && !$binnacle->notification->date ? $binnacle->isNotifiableByMileage() : true;

            return $companyNotifications && $notificationsByMileage;
        });

        return $notifications->sortBy('date');
    }

    /**
     * @param Company $company
     * @param $vehicleReport
     * @param $dateReport
     * @param $withEndDate
     * @param $dateEndReport
     * @param bool $sortDescending
     * @return object
     */
    public function report(Company $company, $vehicleReport, $dateReport, $withEndDate, $dateEndReport, $sortDescending = false)
    {
        $vehicles = ($vehicleReport == 'all') ? $company->vehicles : $company->vehicles()->where('id', $vehicleReport)->get();

        $binnacles = Binnacle::whereIn('vehicle_id', $vehicles->pluck('id'))->where(function ($query) use ($dateReport, $dateEndReport) {
            $query->whereBetween('date', ["$dateReport 00:00:00", "$dateEndReport 23:59:59"])->orWhere('mileage', '>', 0);
        })->get();

        return (object)[
            'company' => $company,
            'vehicleReport' => $vehicleReport,
            'dateReport' => $dateReport,
            'withEndDate' => $withEndDate,
            'dateEndReport' => $dateEndReport,
            'binnacles' => $binnacles->sortBy('date', 0, $sortDescending)->sortBy('id'),
            'isNotEmpty' => $binnacles->isNotEmpty(),
            'sortDescending' => $sortDescending,
        ];
    }

    /**
     * Export report to excel format
     *
     * @param $report
     */
    public function export($report)
    {
        $vehicleIssuesGroups = $report->vehicleIssues->groupBy('issue_uid');

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
        }

        PCWExporterService::excel([
            'fileName' => __('Vehicle issues') . " " . __('Vehicles') . " $report->dateReport",
            'title' => __('Vehicle issues') . " " . __('Vehicles') . " $report->dateReport",
            'subTitle' => __('Vehicle issues') . " " . __('Vehicles'),
            'data' => $dataExcel
        ]);
    }
}
