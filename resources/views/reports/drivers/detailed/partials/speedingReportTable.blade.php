<div class="col-md-8 col-md-offset-2 col-sm-12 col-xs-12 m-t-10">
    @php
        $speedingReport = (new \App\Services\Reports\Routes\SpeedingService())->groupByEvent($dispatchRegister->speedingReport)
    @endphp

    @if( $speedingReport->isNotEmpty() )
        <script>$('.badge-speeding-{{ $dispatchRegister->id }}').html('{{ count($speedingReport) }}').removeClass('hide')</script>
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
                        @lang('Speed')
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
                            {{ $speeding->speed }}
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

        <script>
            $('.collapse-speeding-location-image').on('show.bs.collapse',function(){
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
                @lang('The driver haven´t speeding report in this round trip')
            </div>
        </div>
    @endif
</div>