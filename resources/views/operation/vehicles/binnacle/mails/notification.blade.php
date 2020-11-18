@extends('layouts.blank', ['mail' => true])

<div class="row" style="background: white">
    <div class=" panel-inverse col-lg-8 col-lg-offset-2 col-md-12 col-sm-12 col-xs-12">
        <div class="panel-heading">
            <h3 class="text-white label-vehicles">
                <strong>
                    <i class="fa fa-exclamation-triangle"></i> @lang('Binnacle vehicle notifications')
                </strong>
            </h3>
            <p class="text-white label-vehicles">
                <strong>
                    <i class="fa fa-exclamation-triangle"></i> {{ $binnacles->count() }} @lang('registers')
                </strong>
                <br>
                <small class="text-muted"><i class="fa fa-car"></i> {{ $binnacles->groupBy('vehicle_id')->count() }} @lang('Vehicles')</small>
            </p>
        </div>
        <div class="tab-content  p-0">
            <div class="table-responsive">
                <!-- begin table -->
                <table align="center" border="1" cellpadding="0" cellspacing="0" width="95%">
                    <thead>
                    <tr class="inverse">
                        <th bgcolor="#00283b" style="padding: 10px 0 10px 0;text-align: center; vertical-align: middle;color: #d0d0d0"><i class="fa fa-car" aria-hidden="true"></i>@lang('Vehicle')</th>
                        <th bgcolor="#00283b" style="padding: 10px 0 10px 0;text-align: center; vertical-align: middle;color: #d0d0d0"><i class="fa fa-wrench" aria-hidden="true"></i>@lang('Type')</th>
                        <th bgcolor="#00283b" style="padding: 10px 0 10px 0;text-align: center; vertical-align: middle;color: #d0d0d0"><i class="icon-bag" aria-hidden="true"></i>@lang('Observations')</th>
                        <th bgcolor="#00283b" style="padding: 10px 0 10px 0;text-align: center; vertical-align: middle;color: #d0d0d0"><i class="fa fa-calendar" aria-hidden="true"></i>@lang('Expiration date')</th>
                        <th bgcolor="#00283b" style="padding: 10px 0 10px 0;text-align: center; vertical-align: middle;color: #d0d0d0"><i class="fa fa-calendar" aria-hidden="true"></i>@lang('Notification date')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $binnaclesByVehicles = $binnacles->groupBy('vehicle_id');
                    @endphp
                    @foreach($binnaclesByVehicles as $binnacles)
                        @foreach($binnacles->sortBy('date') as $binnacle)
                            @php
                                $vehicle = $binnacle->vehicle;
                                $type = $binnacle->type;
                                $notification = $binnacle->notification;
                            @endphp
                            <tr style="padding: 10px 0 10px 0;text-align: center; vertical-align: middle">
                                <th class="bg-inverse text-white">
                                    {{ $vehicle->number }}
                                </th>
                                <td>
                                    <label class="label label-{{ $type->css_class }} tooltips" data-title="{{ $type->description }}">{{ $type->name }}</label>
                                </td>
                                <td style="padding: 10px;text-align: left; vertical-align: middle">{{ $binnacle->observations }}</td>
                                <td>{{ $binnacle->date->toDateString() }}</td>
                                <td>{{ $notification ? $notification->date->toDateString() : $binnacle->date->toDateString() }}</td>
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
