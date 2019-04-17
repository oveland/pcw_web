@php
    $speedingReportByVehicles = $speedingReport->report;
@endphp
@if(count($speedingReportByVehicles))
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="{{ route('report-vehicle-speeding-search-report') }}?company={{ $speedingReport->companyReport }}&date-report={{ $speedingReport->dateReport }}&route-report={{ $speedingReport->routeReport }}&type-report={{ $speedingReport->typeReport }}&export=true" class="btn btn-lime btn-rounded bg-lime-dark btn-sm tooltips"
                   title="@lang('Export excel')" data-placement="bottom">
                    <i class="fa fa-file-excel-o"></i>
                </a>

                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-lime " data-click="panel-expand"
                   title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
            </div>

            <h5 class="text-white label-vehicles">{{ count($speedingReportByVehicles) }} @lang('Vehicles') @lang('with') @lang('Speeding')</h5>
        </div>
        <div class="tab-content panel">
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <div class="widge p-t-0 report-by-vehicle-container">
                        <div class="widget-header bg-inverse m-0 row">
                            <div class="col-md-8 p-0">
                                <input type="number" class="form-control input-sm col-md-4 search-vehicle-list" placeholder="@lang('Vehicle')"/>
                            </div>
                            <div class="col-md-1">
                                <i class="fa fa-times btn-clear-search btn btn-default btn-xs" onclick="$(this).parents('.label-vehicles').find('input').val('').keyup()"></i>
                            </div>
                        </div>
                        <div data-scrollbar="true" data-height="400px" data-distance="0px">
                            <ul class="widget-todolist">
                                @foreach($speedingReportByVehicles as $vehicleId => $speedingReport)
                                    @php
                                        $vehicle = \App\Models\Vehicles\Vehicle::find($vehicleId);
                                    @endphp
                                    <li id="vehicle-list-{{ $vehicle->number }}" class="vehicle-list accordion-toggle accordion-toggle-styled {{ $loop->first ? 'collapsed':'' }} accordion-vehicles" data-toggle="collapse" data-parent="#accordion-vehicles" data-target="#vehicle-{{ $vehicleId }}" {{ $loop->first ? 'aria-expanded=true':'' }}>
                                        <div class="checkbox">
                                            <label>
                                                <i class="fa fa-car text-muted"></i>
                                            </label>
                                        </div>
                                        <div class="info info-vehicle-list">
                                            <h4>{{ $vehicle->number  }} | {{ $vehicle->plate  }}</h4>
                                            <p class="f-s-10 text-bold">
                                                {{ count($speedingReport) }} @lang('Speeding')
                                            </p>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div id="accordion-vehicles" class="col-md-6 col-lg-8 col-sm-12 col-sm-12">
                    @foreach($speedingReportByVehicles as $vehicleId => $speedingReport)
                        @php
                            $vehicle = \App\Models\Vehicles\Vehicle::find($vehicleId);
                        @endphp
                        <div id="vehicle-{{ $vehicleId }}" class="panel-collapse collapse {{ $loop->first ? 'in':'' }}" aria-expanded="false">
                            <div class="panel panel-white panel-with-tabs">
                                <div class="panel-heading">
                                    <h4 class="panel-title">{{ $vehicle->number  }} | {{ $vehicle->plate  }}</h4>
                                    <p class="f-s-10 text-bold">
                                        {{ count($speedingReport) }} @lang('Speeding')
                                    </p>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover table-valign-middle table-report">
                                        <thead>
                                        <tr class="inverse">
                                            <th>
                                                <i class="fa fa-list"></i>
                                            </th>
                                            <th>
                                                <i class="fa fa-clock-o"></i><br>
                                                @lang('Time')
                                            </th>
                                            <th>
                                                <i class="fa fa-tachometer"></i><br>
                                                @lang('Speed') Km/h
                                            </th>
                                            <th>
                                                <i class="fa fa-rocket"></i><br>
                                                @lang('Actions')
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($speedingReport as $speeding)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{!! $speeding->time->toTimeString() ?? '' !!}</td>
                                                <td class="text-{{ $speeding->isTruncated()? 'muted':'' }}">
                                                    {{ number_format($speeding->speed,2, ',', '') }}
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning btn-location tooltips" data-toggle="collapse" data-target="#image-{{ $speeding->id }}" data-title="@lang('Location')">
                                                        <i class="fa fa-map-marker"></i>
                                                        <span>@lang('Location')</span>
                                                    </button>
                                                    <span id="address-{{ $speeding->id }}" class="tooltips" data-title="@lang('Address')"></span>
                                                    <button class="btn btn-sm btn-info btn-show-address" onclick="$(this).parent('td').find('.btn-location').find('span').slideUp(1000)"
                                                            data-url="{{ route('report-vehicle-speeding-geolocation-address',['speeding'=>$speeding->id]) }}"
                                                            data-target="#address-{{ $speeding->id }}">
                                                        <i class="fa fa-refresh faa-spin animated-hover hide"></i>
                                                        <span>@lang('Address')</span>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr id="image-{{ $speeding->id }}" class="collapse fade collapse-speeding-location-image" data-url="{{ route('report-vehicle-speeding-geolocation-image',['speeding'=>$speeding->id]) }}">
                                                <td colspan="4" class="text-center">
                                                    <i class="fa fa-2x fa-cog fa-spin text-muted"></i>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <script type="application/javascript">
        //handleSlimScroll();
        $('.report-by-vehicle-container div[data-scrollbar="true"]').slimScroll({
            width: 'auto',
            height: '350px',
            size: '3px',
            position: 'right',
            color: '#0e7685',
            alwaysVisible: false,
            distance: '0px',
            railVisible: true,
            railColor: '#b1d3d6',
            railOpacity: 0.3,
            wheelStep: 10,
            allowPageScroll: true,
            disableFadeOut: false
        });

        $('.collapse-speeding-location-image').on('show.bs.collapse',function(){
            var img = $('<img>').attr('src',$(this).data('url'));
            $(this).find('td').empty().append( img );
        });
    </script>
@else
    <div class="alert alert-success alert-bordered fade in m-b-10 col-md-6 col-md-offset-3">
        <div class="col-md-2" style="padding-top: 10px">
            <i class="fa fa-3x fa-exclamation-circle"></i>
        </div>
        <div class="col-md-10">
            <span class="close pull-right" data-dismiss="alert">×</span>
            <h4><strong>@lang('Hey!')</strong></h4>
            <hr class="hr">
            @lang('The date haven´t a speeding report')
        </div>
    </div>
@endif