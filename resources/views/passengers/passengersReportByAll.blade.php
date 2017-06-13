@if(count($historySeats))
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="" class="btn btn-lime bg-lime-dark btn-export btn-sm" style="color: white !important;"
                   onclick="$(this).attr('href','{{ route('passengers-search-report')  }}?export=true&'+$('.form-search-report').serialize())">
                    <i class="fa fa-file-excel-o"></i> @lang('Export excel')
                </a>
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-lime " data-click="panel-expand" title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
            </div>
            <h5 class="text-white m-t-10"><i class="fa fa-user-circle" aria-hidden="true"></i> @lang('Register historic')</h5>
        </div>
        <div class="panel-content row">
            <div id="report-tab-table" class="table-responsive col-md-12x">
                <!-- begin table -->
                <table class="table table-bordered table-striped table-hover table-valign-middle">
                    <thead>
                    <tr class="inverse">
                        <th>N°</th>
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
                    @php($totalKm = 0)
                    @foreach($historySeats as $historySeat)
                        <tr>
                            <td>{{$loop->index+1}}</td>
                            <td>{{$historySeat->plate}}</td>
                            <td>{{$historySeat->seat}}</td>
                            <td>{{$historySeat->active_time?date('H:i:s',strtotime(explode(" ",$historySeat->active_time)[1])):__('Still busy')}}</td>
                            @if($historySeat->inactive_time)
                                <td>{{date('H:i:s',strtotime(explode(" ",$historySeat->inactive_time)[1]))}}</td>
                                <td>{{date('H:i:s',strtotime($historySeat->busy_time))}}</td>
                                @php($km=$historySeat->busy_km/1000)
                                @php($totalKm += $km)
                                <td>{{number_format($km, 2, ',', '.')}}</td>
                            @else
                                <td class="text-center" colspan="3">@lang('Still busy')</td>
                            @endif
                            <td>
                                <a href="javascript:;" class="btn btn-sm btn-grey btn-link" onclick="gsuccess('@lang('Feature on development')')">
                                    <i class="fa fa-cog fa-spin"></i> @lang('Report detail')
                                </a>
                            </td>
                        </tr>
                    @endforeach
                        <tr class="inverse bg-inverse text-white">
                            <td colspan="6" class="text-right">@lang('Total Km')</td>
                            <td colspan="2" class="text-left">{{number_format($totalKm, 2, ',', '.')}}</td>
                        </tr>
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