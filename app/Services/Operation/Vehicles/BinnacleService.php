<?php

namespace App\Services\Operation\Vehicles;

use App\Models\Vehicles\LastLocation;
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
        if (!$binnacle) {
            $binnacle = new Binnacle();
            $action = 'created';
            $update = false;
        }

        $prevDate = $request->get('prev-date');
        $vehicle = Vehicle::find($request->get('vehicle'));
        $type = Type::find($request->get('type'));

        $binnacle->vehicle()->associate($vehicle);
        $binnacle->user()->associate(Auth::user());
        $binnacle->type()->associate($type);

        DB::beginTransaction();

        $yesterdayLocation = LastLocation::whereDate('date', '<', Carbon::now()->toDateString())
            ->where('vehicle_id', $vehicle->id)
            ->orderByDesc('date')
            ->first();

        $prevDateLocation = LastLocation::whereDate('date', '<', $prevDate)
            ->where('vehicle_id', $vehicle->id)
            ->orderByDesc('date')
            ->first();

        if ($prevDateLocation) {
            $binnacle->mileage = $prevDateLocation->mileage;
            $binnacle->mileage_odometer = $prevDateLocation->odometer;
            $binnacle->mileage_route = $prevDateLocation->mileage_route;
        } else {
            $binnacle->mileage = $yesterdayLocation->mileage ?? 0;
            $binnacle->mileage_odometer = $yesterdayLocation->odometer ?? 0;
            $binnacle->mileage_route = $yesterdayLocation->mileage_route ?? 0;
        }


        $response = collect([
            'success' => true,
            'message' => __("Binnacle register $action successfully")
        ]);

        $binnacle = $binnacle->fill([
            'date' => $request->get('expiration-date'),
            'prev_date' => $request->get('prev-date'),
            'mileage_expiration' => $request->get('expiration-mileage'),
            'observations' => $request->get('observations')
        ]);


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

            $companyNotifications = !$company || $company->vehicles->contains($binnacle->vehicle->id);
            $isNotifiableByMileage = $binnacle->isNotifiableByMileage();
            $isNotifiableByDate = $binnacle->date && $binnacle->date->isToday();

            return $companyNotifications && !$binnacle->completed && ($isNotifiableByMileage || $isNotifiableByDate);
        });

        return $notifications->sortBy('date');
    }

    public function report(Company $company, $vehicleReport, $dateReport, $withEndDate, $dateEndReport, $sortDescending = false, $includeCompleted = true)
    {
        $vehicles = ($vehicleReport == 'all') ? $company->vehicles : $company->vehicles()->where('id', $vehicleReport)->get();

        $binnacles = Binnacle::whereIn('vehicle_id', $vehicles->pluck('id'))->where(function ($query) use ($dateReport, $dateEndReport) {
            $query->whereBetween('date', ["$dateReport 00:00:00", "$dateEndReport 23:59:59"])->orWhere('mileage', '>', 0);
        });

        if (!$includeCompleted) {
            $binnacles->where('completed', false);
        }

        $binnacles = $binnacles->get();

        return (object)[
            'company' => $company,
            'vehicleReport' => $vehicleReport,
            'dateReport' => $dateReport,
            'withEndDate' => $withEndDate,
            'dateEndReport' => $dateEndReport,
            'binnacles' => $binnacles->sortBy('date', 0, $sortDescending)->sortBy('id'),
            'isNotEmpty' => $binnacles->isNotEmpty(),
            'sortDescending' => $sortDescending,
            'includeCompleted' => $includeCompleted,
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
