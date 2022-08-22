@if($report->isNotEmpty)
    <div class="row" style="background: white">
        <div class=" panel-inverse col-md-12 col-sm-12 col-xs-12">
            <div class="panel-heading">
                <div class="panel-heading-btn hide">
                    <a href="{{ route('operation-vehicles-memo-show') }}?company={{ $report->company->id }}&vehicle-report={{ $report->vehicleReport }}&date-report={{ $report->dateReport }}&date-end-report={{ $report->dateEndReport }}&with-end-date={{ $report->withEndDate }}&export=true" class="btn btn-lime bg-lime-dark btn-sm btn-rounded tooltips" data-title="@lang('Export excel')">
                        <i class="fa fa-file-excel-o"></i>
                    </a>
                </div>
                <p class="text-white label-vehicles">
                    <strong>
                        <i class="fa fa-exclamation-triangle"></i> {{ $report->memos->count() }} @lang('registers')
                    </strong>
                    <br>
                    <small class="text-muted"><i class="fa fa-car"></i> {{ $report->memos->groupBy('vehicle_id')->count() }} @lang('Vehicles')</small>
                </p>
            </div>
            <div class="tab-content  p-0">
                <div class="table-responsive">
                    <!-- begin table -->
                    <table class="table table-bordered table-striped table-hover table-valign-middle table-report">
                        <thead>
                        <tr class="inverse">
                            <th class="text-center"><i class="fa fa-calendar" aria-hidden="true"></i><br>@lang('Date')</th>
                            <th class="text-center"><i class="fa fa-car" aria-hidden="true"></i><br>@lang('Vehicle')</th>
                            <th class="text-center"><i class="icon-bag" aria-hidden="true"></i><br>@lang('Observations')</th>
                            <th class="text-center"><i class="fa fa-user" aria-hidden="true"></i><br>@lang('Created')</th>
                            <th class="text-center"><i class="fa fa-user" aria-hidden="true"></i><br>@lang('Edited')</th>
                            <th class="text-center"><i class="fa fa-rocket" aria-hidden="true"></i><br>@lang('Actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $memosByVehicles = $report->memos->sortBy('date', 0, $report->sortDescending)->groupBy('vehicle_id');
                        @endphp
                        @foreach($memosByVehicles as $memos)
                            @foreach($memos as $memo)
                                @php
                                    $vehicle = $memo->vehicle;
                                    $createdUser = $memo->createdUser;
                                    $editedUser = $memo->createdUser;
                                    $observations = $memo->observations;
                                @endphp
                                <tr class="text-center">
                                    <td class="bg-inverse text-white">
                                        {{ $memo->date->toDateString() }}</td>
                                    <th class="bg-inverse text-white">
                                        {{ $vehicle->number }}<br>
                                        <small class="text-muted">{{ $vehicle->plate }}</small>
                                    </th>
                                    <td class="text-left">{{ $memo->short_observations }}</td>
                                    <td class="">
                                        <span>{{ $memo->createdUser->name }}</span><br>
                                        <small>{{ $memo->created_at }}</small>
                                    </td>
                                    <td class="">
                                        @if($memo->editedUser)
                                            <span>{{ $memo->editedUser->name }}</span><br>
                                            <small>{{ $memo->updated_at }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-circle blue-dark btn-outline tooltips" title="@lang('Detail')" onclick="loadMemoFormDetail({{ $memo->id }})">
                                            <i class="fa fa-search"></i>
                                        </button>

                                        <button class="btn btn-circle yellow-mint btn-outline tooltips" title="@lang('Edit')" onclick="loadMemoFormEdit({{ $memo->id }})">
                                            <i class="fa fa-edit"></i>
                                        </button>

                                        <button class="btn btn-circle red btn-outline tooltips" title="@lang('Delete')" onclick="loadMemoFormDelete({{ $memo->id }})">
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
@endif
