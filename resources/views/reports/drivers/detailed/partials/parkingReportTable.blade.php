<div class="col-md-8 col-md-offset-2 col-sm-12 col-xs-12 m-t-10">
    @php
        $parkingReport = $dispatchRegister->parkingReport;
    @endphp

    @if( $parkingReport && $parkingReport->isNotEmpty() )
        <script>$('.badge-parking-{{ $dispatchRegister->id }}').html('{{ count($parkingReport) }}').removeClass('hide')</script>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover table-valign-middle table-report">
                <thead>
                <tr class="inverse">
                    <th>
                        <i class="fa fa-list text-muted"></i>
                    </th>
                    <th class="col-md-2">
                        <i class="fa fa-clock-o text-muted"></i><br>
                        @lang('Time')
                    </th>
                    <th data-sorting="disabled">
                        <i class="fa fa-search text-muted"></i><br>
                        @lang('Details')
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach( $parkingReport as $parking )
                    <tr>
                        <td class="bg-inverse text-white text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">
                            {{ $parking->date->toTimeString() ?? '' }}
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-warning btn-location tooltips" data-toggle="collapse" data-target="#image-{{ $parking->id }}" title="@lang('Location')">
                                <i class="fa fa-map-marker"></i>
                                <span>@lang('Location')</span>
                            </button>
                            <span id="address-{{ $parking->id }}" class="tooltips" data-title="@lang('Address')"></span>
                            <button class="btn btn-sm btn-info btn-show-address"
                                    data-url="{{ route('report-vehicle-parked-geolocation-address',['parkingReport'=>$parking->id]) }}"
                                    data-target="#address-{{ $parking->id }}">
                                <i class="fa fa-refresh faa-spin animated-hover hide"></i>
                                <span>@lang('Address')</span>
                            </button>
                        </td>
                    </tr>
                    <tr id="image-{{ $parking->id }}" class="collapse fade collapse-parked-location-image" data-url="{{ route('report-vehicle-parked-geolocation-image',['parkingReport'=>$parking->id]) }}">
                        <td colspan="4">
                            <div class="text-center">
                                <i class="fa fa-2x fa-cog fa-spin text-muted"></i>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <script type="application/javascript">
            $('.collapse-parked-location-image').on('show.bs.collapse',function(){
                var el = $(this);
                var btnLocation = el.parents('td').find('.btn-location');
                var iconBtnLocation = btnLocation.find('i');
                btnLocation.addClass('disabled');
                iconBtnLocation.removeClass('fa-map-marker').addClass('fa-cog fa-spin');

                var img = $('<img>').attr('src',el.data('url'));
                el.find('div').empty().append( img );

                setTimeout(function(){
                    btnLocation.removeClass('disabled');
                    iconBtnLocation.addClass('fa-map-marker').removeClass('fa-cog fa-spin');
                },1000);
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
                @lang('The driver haven´t parking report in this round trip')
            </div>
        </div>
    @endif
</div>