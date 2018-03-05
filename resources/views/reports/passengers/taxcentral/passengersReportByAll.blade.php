@if(count($historySeats))
    @php($threshold_km = 20000)
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="" class="btn btn-lime bg-lime-dark btn-export btn-sm" style="color: white !important;"
                   onclick="$(this).attr('href','{{ route('report-passengers-taxcentral-search-report')  }}?export=true&'+$('.form-search-report').serialize())">
                    <i class="fa fa-file-excel-o"></i> @lang('Export excel')
                </a>
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-lime " data-click="panel-expand" title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
            </div>
            <h5 class="text-white m-t-10">
                <i class="fa fa-user-circle" aria-hidden="true"></i> @lang('Register historic') - @lang('All Routes'):
                {{ collect($historySeats->where('busy_km','>',$threshold_km)->pluck('busy_km')->count())[0] }} @lang('passengers')
                {{ number_format(collect($historySeats->where('busy_km','>',$threshold_km)->pluck('busy_km')->sum())[0]/1000,'2',',','.') }} @lang('Km in total')
            </h5>
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
                        <tr class="{{ $historySeat->busy_km>$threshold_km?'':'text-danger' }}">
                            <td>{{$loop->index+1}}</td>
                            <td>{{$historySeat->plate}}</td>
                            <td>{{$historySeat->seat}}</td>
                            <td>{{$historySeat->active_time?date('H:i:s',strtotime(explode(" ",$historySeat->active_time)[1])):__('Still busy')}}</td>
                            @if($historySeat->inactive_time)
                                <td>{{date('H:i:s',strtotime(explode(" ",$historySeat->inactive_time)[1]))}}</td>
                                <td>{{date('H:i:s',strtotime($historySeat->busy_time))}}</td>
                                @php($km=$historySeat->busy_km/1000)
                                @php($historySeat->busy_km>$threshold_km?($totalKm += $km):null )
                                <td class="{{ $historySeat->busy_km>$threshold_km?'':'danger' }}">{{number_format($km, 2, ',', '.')}}</td>
                            @else
                                <td class="text-center" colspan="3">@lang('Still busy')</td>
                            @endif
                            <td>
                                <a href="javascript:;"
                                   data-url="{{ route('report-passengers-taxcentral-seat-detail',['historySeat'=>$historySeat->id]) }}"
                                   class="btn btn-sm btn-grey btn-link btn-show-trajectory-seat-report">
                                    <i class="fa fa-cog fa-spin"></i> @lang('Report detail')
                                </a>
                            </td>
                        </tr>
                    @endforeach
                        <tr class="inverse bg-inverse text-white">
                            <td colspan="6" class="text-right">@lang('Total Km')</td>
                            <td colspan="2" class="text-left">{{ number_format($totalKm, 2, ',', '.') }} Km</td>
                        </tr>
                        <tr class="inverse bg-inverse text-white">
                            <td colspan="6" class="text-right">@lang('Total passengers')</td>
                            <td colspan="2" class="text-left">
                                {{ collect($historySeats->where('busy_km','>',$threshold_km)->pluck('busy_km')->count())[0] }}
                            </td>
                        </tr>
                        @php($routeTaxCentral = \App\Route::find(158))
                        <tr class="inverse bg-inverse text-white">
                            <td colspan="6" class="text-right">@lang('Route distance') (Km)</td>
                            <td colspan="2" class="text-left">
                                <input type="number" class="form-control input-sm"
                                       min="0"
                                       max="1000"
                                       data-total-km="{{ $totalKm }}"
                                       placeholder="@lang('Route distance')"
                                       value="{{ $routeTaxCentral->distance }}" style="width:50%"
                                       onchange="$('#passengers_by_total_km').html(
                                           (numeral( parseInt( $(this).data('total-km') ) / parseInt( $(this).val() ) ).format('0.00')).toString().replace('.',',')
                                       )"
                                >
                            </td>
                        </tr>
                        <tr class="inverse bg-inverse text-white">
                            <td colspan="6" class="text-right">@lang('Passengers by Km')</td>
                            <td colspan="2" class="text-left">
                                <span id="passengers_by_total_km">
                                    {{ number_format($totalKm/$routeTaxCentral->distance, 2, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <!-- end table -->
            </div>
        </div>
    </div>
@else
    @include('partials.alerts.noRegistersFound')
@endif