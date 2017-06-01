@if(count($historySeats))
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-lime " data-click="panel-expand" title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
            </div>
            <h4 class="panel-title"><i class="fa fa-user-circle" aria-hidden="true"></i> @lang('Register historic')</h4>
        </div>
        <div class="row">
            <div class="table-responsive col-md-12">
                <!-- begin table -->
                <table id="data-table" class="table table-bordered table-striped table-hover table-valign-middle">
                    <thead>
                        <tr class="inverse">
                            <th>@lang('Vehicle')</th>
                            <th>@lang('Seat')</th>
                            <th>@lang('Event active time')</th>
                            <th>@lang('Event inactive time')</th>
                            <th>@lang('Active time')</th>
                            <th>@lang('Active kilometers')</th>
                            <th>@lang('Actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($historySeats as $historySeat)
                        <tr>
                            <td>{{$historySeat->plate}}</td>
                            <td>{{$historySeat->seat}}</td>
                            <td>{{date('H:i:s',strtotime($historySeat->active_time))}}</td>
                            <td>{{date('H:i:s',strtotime($historySeat->inactive_time))}}</td>
                            <td>{{date('H:i:s',strtotime($historySeat->busy_time))}}</td>
                            <td>{{number_format($historySeat->busy_km/1000, 2, '.', ',')}}</td>
                            <td>
                                <a href="javascript:;" class="btn btn-sm btn-grey btn-link" onclick="gsuccess('@lang('Feature on development')')">
                                    <i class="fa fa-cog fa-spin"></i> @lang('Report detail')
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <!-- end table -->
            </div>
        </div>
    </div>
@else
    <div class="alert alert-warning alert-bordered fade in m-b-10 col-md-6 col-md-offset-3">
        <div class="col-md-2" style="padding-top: 10px">
            <i class="fa fa-3x fa-exclamation-circle"></i>
        </div>
        <div class="col-md-10">
            <span class="close pull-right" data-dismiss="alert">×</span>
            <h4><strong>@lang('Ups!')</strong></h4>
            <hr class="hr">
            @lang('No registers found')
        </div>
    </div>
@endif