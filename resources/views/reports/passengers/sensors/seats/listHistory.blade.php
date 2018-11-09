@if(count($passengers))
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="javascript:void(0);" class="btn btn-xs btn-icon btn-circle btn-lime " data-click="panel-expand"
                   title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
            </div>
            <h5 class="text-white label-vehicles m-b-0">
                <i class="ion-clipboard"></i> @lang('List counter passengers')
                @include('admin.counter.report.totalInfo')
                @include('partials.pagination.totalInfo',['paginator' => $passengers ])
            </h5>
        </div>
        <div class="tab-content panel">
            <div class="row">
                <div class="col-md-3 p-20 p-b-0" style="border-radius: 10px;border: 1px solid #cdcdcd;-webkit-box-shadow: 3px 0 23px -9px rgba(0,0,0,0.75);-moz-box-shadow: 3px 0 23px -9px rgba(0,0,0,0.75);box-shadow: 3px 0 23px -9px rgba(0,0,0,0.75);">
                    <div class="seating-template text-center">
                        {!! \App\Services\Reports\Passengers\SeatDistributionGualasService::makeHtmlTemplate($passengers->first()) !!}
                    </div>

                    <hr class="m-0 m-t-10 m-b-10">

                    <div class="row">
                        <small class="col-md-4 text-left p-l-20">
                            <i class="fa fa-clock-o"></i>
                            <span class="play-time"></span>
                        </small>
                        <small class="col-md-4 text-center">
                            <i class="fa fa-flag"></i>
                            <span class="play-route">@lang('No Route')</span>
                        </small>
                        <small class="col-md-4 text-right p-r-25"><i class="fa fa-users"></i>
                            <span class="play-passengers"></span>
                        </small>
                    </div>

                    <!-- begin progress-bar -->
                    <div class="col-md-12 m-t-10 m-b-10">
                        <div class="progress progress-sm progress-striped active m-0">
                            <div class="progress-bar progress-bar-info play-progress" style="width: 0">0</div>
                        </div>
                        <input id="ex1" title="@lang('Sidebar')" data-slider-id='ex1Slider' type="text"/>
                    </div>
                    <!-- end progress-bar -->

                    <div class="col-md-12 text-center" style="top:-20px">
                        <button class="btn btn-sm btn-primary btn-play tooltips" data-title="@lang('Play')" data-placement="bottom"
                                onclick="pause();play();running=true;">
                            <i class="icon-control-play"></i>
                        </button>
                        <button class="btn btn-sm btn-warning btn-pause tooltips" data-title="@lang('Pause')" data-placement="bottom"
                                onclick="pause();running=false;" style="display: none">
                            <i class="icon-control-pause"></i>
                        </button>
                        <button class="btn btn-sm btn-default btn-stop tooltips faa-parent animated-hover" data-title="@lang('Stop')" data-placement="bottom"
                                onclick="stop();running=false;">
                            <i class="fa faa-pulse fa-stop"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-9">
                    <div id="google-map-light-dream" class="col-md-12 p-0" style="height: 500px"></div>
                </div>
                <div class="col-md-12 p-0">{{ $passengers->links() }}</div>
            </div>
        </div>
    </div>

    <script type="application/javascript">
        @php( $seatingStatusReport = collect([]) )
        @foreach($passengers as $passenger)
            @php( $seatingStatusReport->push(\App\Services\Reports\Passengers\SeatDistributionGualasService::getSeatingStatus($passenger)) )
        @endforeach
        let seatingStatusReport = JSON.parse('{!! $seatingStatusReport->toJson() !!}');

        let indexes = seatingStatusReport.length;
        let currentIndex = 0;
        let running = false;

        function render(){
            let report = seatingStatusReport[currentIndex];

            console.log(report.hexSeating);
            $('.hex-seating').html(report.hexSeating);
            $.each(report.seatingStatus, function (container, seating) {
                console.log(container,seating);

                $.each(seating, function (seat, status) {
                    let seatView = $('.data-'+container).find('#seat-'+seat);
                    seatView.removeClass('seat-active').removeClass('seat-inactive');
                    seatView.addClass( status === 1 ? 'seat-active':'seat-inactive' );
                });

            });

            /* Track events on google maps */
            let location = report.location;
            let time = report.time;
            let passengers = report.passengers;
            let route = report.route;

            if (trackingPoint) {
                trackingPoint.marker.setPosition(new google.maps.LatLng({
                    lat: parseFloat(location.latitude),
                    lng: parseFloat(location.longitude)
                }));
                trackingPoint.infowindow.setContent(makeContentMarkerMain(report));
                trackingPoint.infowindow.open(map,trackingPoint.marker);
            }

            $('.play-time').html(time);
            $('.play-route').html(route);
            $('.play-passengers').html(passengers);

            $('.play-progress').css('width',((currentIndex+1)*100/indexes)+'%').html(parseInt((currentIndex+1)*100/indexes)+'%');
            $('#ex1').bootstrapSlider('setValue',currentIndex+1);
        }

        function play(){
            $('.btn-pause').show();$('.btn-play').hide();
            if( currentIndex < indexes ){
                tout = setTimeout(function (){
                    render();
                    currentIndex++;
                    play();
                },200);
            }
        }

        function stop(){
            $('.btn-pause').hide();$('.btn-play').show();
            clearTimeout(tout);
            currentIndex = 0;
            render();
        }

        function pause(){
            $('.btn-pause').hide();$('.btn-play').show();
            clearTimeout(tout);
        }

        function initSlider(){
            $('#ex1').bootstrapSlider({
                min:0,
                max:indexes,
                step:1,
                value:0,
                formatter: function(value) {
                    return '@lang('Register'): ' + value;
                }
            }).on('slideStart',function(data){
                pause();
            }).on('slide',function(data){
                currentIndex = data.value;
                render();
            }).on('slideStop',function(data){
                currentIndex = data.value;
                render();
                if(running)play();
            });
        }

        initSlider();

        /*
        *    Functions for tracking events seating on google Maps
        */
        let markers = [];
        let trackingPoint = {
            marker:null,
            infowindow:null
        };
        let infowindows = [];
        let bounds = new google.maps.LatLngBounds();

        initializeMap();

        setTimeout(function(){
            seatingStatusReport.forEach(function(report,i){
                let location = report.location;
                let contentMarker = makeContentMarkerMain(report);

                addMarker(
                    new google.maps.LatLng({
                        lat: parseFloat(location.latitude),
                        lng: parseFloat(location.longitude)
                    }),
                    contentMarker,
                    '{{ asset('img/point-map-blue.png') }}'
                );

                /* Set marker main as first location report */
                if( i === 0 ){
                    trackingPoint = addMarker(
                        new google.maps.LatLng({
                            lat: parseFloat(location.latitude),
                            lng: parseFloat(location.longitude)
                        }),
                        contentMarker, null
                    );
                }
            });
            //now fit the map to the newly inclusive bounds
            map.fitBounds(bounds);

            render();
        },500);

        // Adds a marker to the map and push to the array.
        function addMarker(location,content,icon) {
            let marker = new google.maps.Marker({
                position: location,
                map: map
            });

            if( icon )marker.setIcon(icon);

            //extend the bounds to include each marker's position
            bounds.extend(marker.position);

            let infowindow = new google.maps.InfoWindow({
                content: content
            });

            marker.addListener('click', function() {
                infowindow.open(marker.get('map'), marker);
            });

            markers.push(marker);
            infowindows.push(infowindow);

            return {
                marker:marker,
                infowindow: infowindow
            };
        }

        function makeContentMarkerMain(report){
            let time = report.time;
            let route = report.route;
            let passengers = report.passengers;
            let passengersPlatform = report.passengersPlatform;
            let vehicleStatus = report.vehicleStatus;
            let vehicleStatusIconClass = report.vehicleStatusIconClass;
            let vehicleStatusMainClass = report.vehicleStatusMainClass;

            return "<div class='p-5'>"+
                    "<strong><i class='fa fa-clock-o'></i> @lang('Time'):</strong> " + time + "<br>" +
                    "<strong><i class='fa fa-flag'></i> @lang('Route'):</strong> " + route + "<br>"+
                    "<strong><i class='fa fa-users'></i> @lang('Passengers'):</strong> " + passengers + "<br>"+
                    "<strong><i class='fa fa-users'></i> @lang('Platform'):</strong> " + passengersPlatform + "<hr class='hr'>"+
                    "@lang('GPS'): <strong><i class='text-" + vehicleStatusMainClass + " " + vehicleStatusIconClass + "'></i> </strong> " + vehicleStatus + "<br>" +
            "</div>";
        }

        hideSideBar();
    </script>
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