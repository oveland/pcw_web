@if(count($vehicleStatusReports))
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-info " data-click="panel-expand" title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
            </div>
            <div class="text-white m-t-10">
                <ul class="nav nav-pills nav-pills-success">
                    @foreach($vehicleStatusReports as $vehicleId => $vehicleStatusReport)
                    @php( $vehicle = \App\Models\Vehicles\Vehicle::find($vehicleId) )
                    <li class="{{ $loop->first ? 'active':'' }} tooltips" data-title="{{ $vehicle->plate }} ({{ $vehicle->company->short_name }})">
                        <a href="#vehicle-{{ $vehicle->id }}" data-toggle="tab" aria-expanded="true">
                            <i class="fa fa-car" aria-hidden="true"></i> {{ $vehicle->number }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="tab-content p-0">
            @foreach($vehicleStatusReports as $vehicleId => $vehicleStatusReport)
                @php( $vehicle = \App\Models\Vehicles\Vehicle::find($vehicleId) )
                <div id="vehicle-{{ $vehicle->id }}" class="table-responsive tab-pane fade {{ $loop->first ? 'active in':'' }}">
                <!-- begin table -->
                    <table class="table table-bordered table-striped table-hover table-valign-middle">
                        <thead>
                        <tr class="inverse">
                            <th class="text-center">N°</th>
                            <th class="text-center"><i class="fa fa-calendar" aria-hidden="true"></i> @lang('Date') @lang('Status')</th>
                            <th class="text-center"><i class="fa fa-calendar" aria-hidden="true"></i> @lang('Date') @lang('GPS')</th>
                            <th class="text-center"><i class="fa fa-podcast" aria-hidden="true"></i> @lang('Status')</th>
                            <th class="text-center"><i class="fa fa-tachometer" aria-hidden="true"></i> @lang('Speed')</th>
                            <th class="text-center" width="40%"><i class="fa fa-map-marker" aria-hidden="true"></i> @lang('Location')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($vehicleStatusReport as $report)
                            <tr class="text-center">
                                <td >{{ $loop->iteration }}</td>
                                <td >
                                    <small class="text-muted">{{ $report->created_at->toDateString() }}</small><br>
                                    {{ $report->created_at->toTimeString() }}
                                </td>
                                <td >
                                    <small class="text-muted">{{ $report->date->toDateString() }}</small><br>
                                    {{ $report->time }}
                                </td>
                                <td >{{ $report->status->des_status }}</td>
                                <td >{{ $report->speed }}</td>
                                <td width="40%">
                                    <button class="btn btn-xs btn-warning btn-location tooltips" data-toggle="collapse" data-target="#image-{{ $report->id }}" data-title="@lang('Location')">
                                        <i class="fa fa-map-o"></i>
                                    </button>
                                    <button class="btn btn-xs btn-info tooltips" data-toggle="collapse" data-target="#frame-{{ $report->id }}" data-title="@lang('Frame')">
                                        <i class="fa fa-code"></i>
                                    </button>
                                    <div id="image-{{ $report->id }}" class="collapse fade collapse-geolocation-image col-md-12 text-center" data-url="{{ route('report-vehicle-status-geolocation-image',['vehicleStatusReport'=>$report->id]) }}">
                                        <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1 col-sm-12 col-xs-12  p-5 m-t-10" style="border-radius: 10px; border: solid 1px lightgray"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr id="frame-{{ $report->id }}" class="text-center collapse">
                                <td colspan="7">
                                    <pre class="pre col-md-12 m-0">{{ $report->frame }}</pre>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <!-- end table -->
                </div>
            @endforeach
        </div>
    </div>

    <script type="text/javascript">
        $('[data-toggle="tooltip"]').tooltip({
            container: 'body'
        });

        $('.collapse-geolocation-image').on('show.bs.collapse',function(){
            var img = $('<img>').attr('src',$(this).data('url'));
            img.css('width', '100%');
            $(this).find('div').empty().append( img );
        });
    </script>
@else
    @include('partials.alerts.noRegistersFound')
@endif