<div class="col-md-8 col-md-offset-2 col-sm-12 col-xs-12 m-t-10">
    @php( $offRoadReport = \App\Services\Reports\Routes\OffRoadService::groupByFirstOffRoadByRoute($dispatchRegister->offRoads)->first() )
    @if( $offRoadReport && $offRoadReport->isNotEmpty() )
        <script>$('.badge-off-road-{{ $dispatchRegister->id }}').html('{{ count($offRoadReport) }}').removeClass('hide')</script>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover table-valign-middle table-report">
                <thead>
                <tr class="inverse">
                    <th><i class="fa fa-list-ol"></i></th>
                    <th>
                        <i class="fa fa-clock-o"></i><br>
                        @lang('Off road time')
                    </th>
                    <th>
                        <i class="fa fa-map"></i><br>
                        @lang('Address')
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($offRoadReport as $offRoad)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">{{ $offRoad->date->toTimeString() }}</td>
                        <td class="text-center">
                            <a href="{{ route('report-route-off-road',['dispatchRegister'=>$dispatchRegister->id]) }}?export=true" class="btn btn-lime bg-lime-dark btn-sm">
                                <i class="fa fa-file-excel-o"></i>
                            </a>
                            <button class="btn btn-sm btn-warning btn-location tooltips" data-toggle="collapse" data-target="#image-{{ $offRoad->id }}" data-title="@lang('Location')">
                                <i class="fa fa-map-marker"></i>
                                <span>@lang('Location')</span>
                            </button>
                            <span id="address-{{ $offRoad->id }}" class="tooltips" data-title="@lang('Address')"></span>
                            <button class="btn btn-sm btn-info btn-show-address" onclick="$(this).parent('td').find('.btn-location').find('span').slideUp(1000)"
                                    data-url="{{ route('report-route-off-road-geolocation-address',['offRoad'=>$offRoad->id]) }}"
                                    data-target="#address-{{ $offRoad->id }}">
                                <i class="fa fa-refresh faa-spin animated-hover hide"></i>
                                <span>@lang('Address')</span>
                            </button>
                        </td>
                    </tr>
                    <tr id="image-{{ $offRoad->id }}" class="collapse fade collapse-off-road-image" data-url="{{ route('report-route-off-road-geolocation-image',['offRoad'=>$offRoad->id]) }}">
                        <td colspan="4" class="text-center">
                            <i class="fa fa-2x fa-cog fa-spin text-muted"></i>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <script>
            $('.collapse-off-road-image').on('show.bs.collapse',function(){
                var img = $('<img>').attr('src',$(this).data('url'));
                $(this).find('td').empty().append( img );
            });
        </script>
    @else
        <div class="alert alert-success alert-bordered fade in m-b-10 col-md-8 col-md-offset-2">
            <div class="col-md-2" style="padding-top: 10px">
                <i class="fa fa-3x fa-exclamation-circle"></i>
            </div>
            <div class="col-md-10">
                <span class="close pull-right" data-dismiss="alert">×</span>
                <h4><strong>@lang('Hey!')</strong></h4>
                <hr class="hr">
                @lang('The driver haven´t off roads list in this round trip')
            </div>
        </div>
    @endif
</div>