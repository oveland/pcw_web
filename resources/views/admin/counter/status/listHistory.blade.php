@if(count($passengers))
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-lime " data-click="panel-expand"
                   title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
            </div>
            <h5 class="text-white label-vehicles">
                <i class="ion-clipboard"></i>
                @lang('List counter passengers')
            </h5>
        </div>
        <div class="tab-content panel">
            <div class="row">
                <div class="table-responsive">
                    <!-- begin table -->
                    <table id="data-table" class="table table-bordered table-striped table-hover table-valign-middle table-report">
                        <thead>
                        <tr class="inverse">
                            <th>
                                <i class="fa fa-calendar text-muted"></i><br>
                                @lang('Date')
                            </th>
                            <th>
                                <i class="fa fa-car text-muted"></i><br>
                                @lang('Vehicle')
                            </th>
                            <th>
                                <i class="fa fa-users text-muted"></i><br>
                                @lang('Passengers')
                            </th>
                            <th>
                                <i class="fa fa-flag text-muted"></i><br>
                                @lang('Route')
                            </th>
                            <th>
                                <i class="fa fa-rocket text-muted"></i><br>
                                @lang('Actions')
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($passengers as $passenger)
                            @php($vehicle = $passenger->vehicle)
                            @php($counterIssue = $passenger->counterIssue )
                            @php($dispatchRegister = $passenger->dispatchRegister )

                            <tr>
                                <td class="text-center">{{ $passenger->date }}</td>
                                <td class="text-center">{{ $vehicle->number }}</td>
                                <td class="text-center">{{ $passenger->total }}</td>
                                <td class="text-center">
                                    @if($dispatchRegister)
                                        <div class="btn btn-info btn-sm btn-block">
                                            {{ $dispatchRegister->route->name }}
                                        </div>
                                    @else
                                        ----
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" data-toggle="collapse" data-target="#frame-{{ $passenger->id }}">
                                        <i class="ion-more"></i>
                                        @lang('See frame')
                                    </button>
                                    @if($counterIssue)
                                        <button class="btn btn-sm btn-danger" onclick="ginfo('@lang('Feature on development')')">
                                            <i class="fa fa-exclamation-triangle"></i>
                                            @lang('Issues')
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            <tr id="frame-{{ $passenger->id }}" class="bg-inverse text-white text-bold collapse fade">
                                <td colspan="5">{{ $passenger->frame }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <!-- end table -->
                </div>
            </div>
        </div>
    </div>
    <script>hideSideBar()</script>
@else
    <div class="alert alert-warning alert-bordered fade in m-b-10 col-md-6 col-md-offset-3">
        <div class="col-md-2" style="padding-top: 10px">
            <i class="fa fa-3x fa-exclamation-circle"></i>
        </div>
        <div class="col-md-10">
            <span class="close pull-right" data-dismiss="alert">×</span>
            <h4><strong>@lang('Ups')!</strong></h4>
            <hr class="hr">
            @lang('The are not list of passengers and counter on this date range')
        </div>
    </div>
@endif