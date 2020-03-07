@php
    use App\Models\Vehicles\VehicleIssue;
    use App\Models\Vehicles\VehicleIssueType;
@endphp

@if($report->isNotEmpty)
    <div class="row" style="background: white">
        <div class=" panel-inverse col-lg-8 col-lg-offset-2 col-md-12 col-sm-12 col-xs-12">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="{{ route('operation-vehicles-issues-show') }}?company={{ $report->company->id }}&vehicle-report={{ $report->vehicleReport }}&date-report={{ $report->dateReport }}&export=true" class="btn btn-lime bg-lime-dark btn-sm btn-rounded tooltips" data-title="@lang('Export excel')">
                        <i class="fa fa-file-excel-o"></i>
                    </a>
                </div>
                <p class="text-white label-vehicles">
                    <strong>
                        <i class="fa fa-exclamation-triangle"></i> {{ $report->vehicleIssues->count() }} @lang('registers')
                    </strong>
                    <br>
                    <small class="text-muted"><i class="fa fa-car"></i> {{ $report->vehicleIssues->groupBy('vehicle_id')->count() }} @lang('Vehicles')</small>
                </p>
            </div>
            <div class="tab-content  p-0">
                <div class="table-responsive">
                    <!-- begin table -->
                    <table class="table table-bordered table-striped table-hover table-valign-middle table-report">
                        <thead>
                        <tr class="inverse">
                            <th class="text-right" colspan="6">

                            </th>
                        </tr>
                        <tr class="inverse">
                            <th class="text-center"><i class="fa fa-car" aria-hidden="true"></i><br>@lang('Vehicle')</th>
                            <th class="text-center"><i class="fa fa-calendar-o" aria-hidden="true"></i><br>@lang('Date')</th>
                            <th class="text-center"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><br>@lang('Type')</th>
                            <th class="text-center"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><br>@lang('Vehicle issue')</th>
                            <th class="text-center hide"><i class="fa fa-user" aria-hidden="true"></i><br>@lang('Driver')</th>
                            <th class="text-center"><i class="fa fa-user" aria-hidden="true"></i><br>@lang('User')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $vehiclesIssues = $report->vehicleIssues->groupBy('vehicle_id');
                        @endphp
                        @foreach($vehiclesIssues as $vehicleIssues)
                            @php
                                $vehicleIssuesGroups = $vehicleIssues->groupBy('issue_uid');
                            @endphp
                            @foreach($vehicleIssuesGroups as $issueUid => $vehicleIssuesGroup)
                                @php
                                    $vehicle = $vehicleIssuesGroup->first()->vehicle;
                                    $vehicleIssuesGroup =  $vehicleIssuesGroup->sortBy('date');

                                    $issueIn = VehicleIssue::where('issue_uid', $issueUid)->where('issue_type_id', VehicleIssueType::IN)->get()->first();

                                    $dateIn = $issueIn ? $issueIn->date : null;
                                @endphp
                                <tr class="text-center">
                                    <th class="bg-inverse text-white" rowspan="{{ $vehicleIssuesGroup->count() + 1 }}">
                                        {{ $vehicle->number }}
                                    </th>
                                </tr>
                                @foreach($vehicleIssuesGroup->sortBy('date', 0, $report->sortDescending) as $issue)
                                    @php
                                        $type = $issue->type;
                                        $duration = $type->id == VehicleIssueType::OUT ? ($dateIn ? $issue->date->diffAsCarbonInterval($dateIn, false)->forHumans() : __('Greater than one day') ) : null;
                                        $driver = $issue->driver;
                                    @endphp
                                    <tr class="text-center">
                                        <td>{{ $issue->date->toDateTimeString() }}</td>
                                        <td>
                                            <label class="label label-{{ $type->css_class }} tooltips" data-title="{{ $type->description }}">{{ $type->name }}</label>
                                            @if($duration)
                                                <small class="tooltips" data-title="@lang('Vehicle issue duration')">
                                                    <label class="label label-inverse"><i class="fa fa-clock-o"></i> {{ $duration }}</label>
                                                </small>
                                            @endif
                                        </td>
                                        <td class="issue-observations text-left">{{ $issue->observations }}</td>
                                        <td class="text=uppercase hide">{{ $driver ? $driver->fullName() : "" }}</td>
                                        <td class="text-uppercase">
                                            @if($issue->user)
                                                <small>{{ $issue->user->username }} - {{ $issue->user->role }}</small>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="bg-inverse text-white">
                                    <td colspan="6" style="padding: 3px"></td>
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
@endif