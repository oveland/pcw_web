@if($report->isNotEmpty)
    <div class="row" style="background: white">
        <div class=" panel-inverse col-md-12 col-sm-12 col-xs-12">
            <div class="panel-heading">
                <div class="panel-heading-btn hide">
                    <a href="{{ route('operation-vehicles-binnacle-show') }}?company={{ $report->company->id }}&vehicle-report={{ $report->vehicleReport }}&date-report={{ $report->dateReport }}&date-end-report={{ $report->dateEndReport }}&with-end-date={{ $report->withEndDate }}&export=true" class="btn btn-lime bg-lime-dark btn-sm btn-rounded tooltips" data-title="@lang('Export excel')">
                        <i class="fa fa-file-excel-o"></i>
                    </a>
                </div>
                <p class="text-white label-vehicles">
                    <strong>
                        <i class="fa fa-exclamation-triangle"></i> {{ $report->binnacles->count() }} @lang('registers')
                    </strong>
                    <br>
                    <small class="text-muted"><i class="fa fa-car"></i> {{ $report->binnacles->groupBy('vehicle_id')->count() }} @lang('Vehicles')</small>
                </p>
            </div>
            <div class="tab-content  p-0">
                <div class="table-responsive">
                    <!-- begin table -->
                    <table class="table table-bordered table-striped table-hover table-valign-middle table-report">
                        <thead>
                        <tr class="inverse">
                            <th class="text-center"><i class="fa fa-car" aria-hidden="true"></i><br>@lang('Vehicle')</th>
                            <th class="text-center"><i class="fa fa-wrench" aria-hidden="true"></i><br>@lang('Type')</th>
                            <th class="text-center"><i class="icon-bag" aria-hidden="true"></i><br>@lang('Observations')</th>
                            <th class="text-center">
                                <i class="fa fa-calendar" aria-hidden="true"></i> @lang('Previous maintenance')
                            </th>
                            <th class="text-center">
                                <i class="fa fa-calendar" aria-hidden="true"></i> @lang('Expiration date')
                            </th>
                            <th class="text-center">
                                <i class="fa fa-calendar" aria-hidden="true"></i> @lang('Notification date')
                            </th>
                            <th class="text-center">
                                <i class="fa fa-road" aria-hidden="true"></i> @lang('Mileage')<br>
                                <small class="text-muted">
                                    @lang('Traveled') / @lang('Expiration')
                                    <hr class="no-padding no-margin">
                                    @lang('Difference')
                                </small>
                            </th>
                            <th class="text-center">
                                <i class="fa fa-road" aria-hidden="true"></i> @lang('Notification mileage')<br>
                                <small class="text-muted">
                                    @lang('Traveled') / @lang('Expiration')
                                    <hr class="no-padding no-margin">
                                    @lang('Difference')
                                </small>
                            </th>
                            <th class="text-center"><i class="fa fa-users" aria-hidden="true"></i> <i class="fa fa-bell faa-ring animated" aria-hidden="true"></i><br>@lang('Notification users')</th>
                            <th class="text-center"><i class="fa fa-user" aria-hidden="true"></i><br>@lang('Created')</th>
                            <th class="text-center"><i class="fa fa-rocket" aria-hidden="true"></i><br>@lang('Actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $binnaclesByVehicles = $report->binnacles->groupBy('vehicle_id');
                        @endphp
                        @foreach($binnaclesByVehicles as $binnacles)
                            @foreach($binnacles->sortBy('date', 0, $report->sortDescending) as $binnacle)
                                @php
                                    $vehicle = $binnacle->vehicle;
                                    $type = $binnacle->type;
                                    $user = $binnacle->user;
                                    $notification = $binnacle->notification;
                                    $notificationUsers = $notification->notificationUsers;
                                @endphp
                                <tr class="text-center">
                                    <th class="bg-inverse text-white">
                                        {{ $vehicle->number }}<br>
                                        <small class="text-muted">{{ $vehicle->plate }}</small>
                                    </th>
                                    <td class="text-left">
                                        <label class="label-type tooltips" data-title="{{ $type->description }}">
                                            <i class='fa fa-circle text-{{ $type->css_class }}'></i> {{ $type->name }}
                                        </label>
                                    </td>
                                    <td class="binnacle-observations text-left">{{ $binnacle->observations }}</td>
                                    <td>
                                        @if($binnacle->prev_date)
                                            {{ $binnacle->prev_date->toDateString() }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($binnacle->date)
                                            {{ $binnacle->date->toDateString() }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($notification && $notification->date)
                                            {{ $notification->date->toDateString() }}
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-center" style="display: inline-block">
                                            @if($binnacle->mileage)
                                                {{ number_format($binnacle->getMileageTraveled(), 1)." Km" }} / {{ "$binnacle->mileage Km" }}
                                                <hr class="no-margin no-padding">
                                                <p class="no-margin no-padding">
                                                    <small>{{ number_format($binnacle->mileage - $binnacle->getMileageTraveled(), 1) }} Km</small>
                                                </p>
                                                @if(Auth::user()->isSuperAdmin())
                                                    <small class="text-muted">
                                                        <small class="tooltips" title="Km Odometer" data-placement="left">
                                                            {{ number_format($binnacle->getMileageTraveled('odometer'), 1)." Km" }}
                                                        </small>
                                                        vs
                                                        <small class="tooltips" title="Km Route" data-placement="left">
                                                            {{ number_format($binnacle->getMileageTraveled('route'), 1)." Km" }}
                                                        </small>
                                                    </small>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-center" style="display: inline-block">
                                            @if($binnacle->mileage && $notification->mileage)
                                                {{ number_format($binnacle->getMileageTraveled(), 1)." Km" }} / {{ "$notification->mileage Km" }}
                                                <hr class="no-margin no-padding">
                                                <p class="no-margin no-padding">
                                                    <small>{{ number_format($notification->mileage - $binnacle->getMileageTraveled(), 1) }} Km</small>
                                                </p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-left">
                                        @foreach($notificationUsers as $notificationUser)
                                            @php
                                                $userNotification = $notificationUser->user;
                                            @endphp

                                            <div class="p-10 m-t-5 m-b-5 btn btn-default btn-outline text-left" style="cursor: text">
                                                <p class="m-0 text-{{ $notificationUser->platform_notified ? 'success tooltips' : '' }}" data-title="@lang('Notified on platform')">
	                                                {{ $userNotification->username }}
                                                    @if($notificationUser->platform_notified)
                                                        <i class="fa fa-check-circle-o text-success"></i>
                                                    @endif
                                                </p>
                                                <small>
                                                    <i class="fa fa-user text-success"></i> {{ $userNotification->username }}
                                                </small>
                                                <br>
                                                <small class="text-{{ $notificationUser->email_notified ? 'success tooltips' : '' }}" data-title="@lang('Notified via email')">
                                                    <i class="fa fa-envelope text-warning"></i> {{ $userNotification->email }}
                                                    @if($notificationUser->email_notified)
                                                        <i class="fa fa-check-circle-o text-success"></i>
                                                    @endif
                                                </small>
                                            </div>
                                        @endforeach
                                    </td>
                                    <td class="">
                                        <span>{{ $user->name }}</span><br>
                                        <small>{{ $binnacle->created_at }}</small>
                                    </td>
                                    <td class="text-center">
                                        @if(!$binnacle->completed)
                                            <button class="btn btn-circle green btn-outline tooltips" title="@lang('Register as completed')" onclick="completeBinnacle({{ $binnacle->id }})">
                                                <i class="fa fa-check"></i>
                                            </button>
                                            <button class="btn btn-circle yellow-mint btn-outline tooltips" title="@lang('Edit')" onclick="loadBinnacleFormEdit({{ $binnacle->id }})">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                        @else
                                            <label class="label-success btn-circle p-10 tooltips text-white" data-title="{{ $binnacle->updated_at }}">
                                                <i class="fa fa-check text-white"></i> @lang('Maintenance completed')
                                            </label>

                                            <button class="btn btn-circle warning btn-outline tooltips" title="@lang('Create other')" onclick="loadBinnacleFormCreate({{ $binnacle->id }})">
                                                <i class="fa fa-copy"></i>
                                            </button>
                                        @endif

                                        <button class="btn btn-circle red btn-outline tooltips" title="@lang('Delete')" onclick="loadBinnacleFormDelete({{ $binnacle->id }})">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                        </tbody>
                    </table>
                    <!-- end table -->
                </div>
            </div>
        </div>
    </div>
@else
    @include('partials.alerts.noRegistersFound')

    <style>
        .btn-create-binnacle{
            position: relative;
            margin: auto;
        }
    </style>
@endif
