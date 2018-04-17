@if(count($passengers))
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-lime " data-click="panel-expand"
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
                <div class="col-md-3 p-20 p-b-0" style="border-radius: 10px;border: 1px solid #cdcdcd;-webkit-box-shadow: 3px 0px 23px -9px rgba(0,0,0,0.75);-moz-box-shadow: 3px 0px 23px -9px rgba(0,0,0,0.75);box-shadow: 3px 0px 23px -9px rgba(0,0,0,0.75);">
                    <div class="seating-template text-center">
                        {!! \App\Services\PCWSeatSensorGualas::makeHtmlTemplate($passengers->first()) !!}
                    </div>

                    <hr class="m-0 m-t-20">
                    <!-- begin progress-bar -->
                    <div class="col-md-12 m-t-10 m-b-5">
                        <div class="progress progress-sm progress-striped active m-0">
                            <div class="progress-bar progress-bar-info play-progress" style="width: 0%">0</div>
                        </div>
                        <input id="ex1" data-slider-id='ex1Slider' type="text"/>
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

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.0.0/css/bootstrap-slider.css" rel="stylesheet"></link>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.0.0/bootstrap-slider.min.js"></script>

    <script type="application/javascript">
        @php( $seatingStatusReport = collect([]) )
        @foreach($passengers as $passenger)
            @php( $seatingStatusReport->push(\App\Services\PCWSeatSensorGualas::getSeatingStatus($passenger)) )
        @endforeach
        var seatingStatusReport = JSON.parse('{!! $seatingStatusReport->toJson() !!}');

        var indexes = seatingStatusReport.length;
        var currentIndex = 0;
        var running = false;

        function render(){
            var report = seatingStatusReport[currentIndex];
            var seatingStatus = report.seatingStatus;
            var seating = seatingStatus.row1;
            jQuery.extend(seating, seatingStatus.row2);// Combine objects (row1 + row2)

            $.each(seating, function (seat, status) {
                var seatView = $('#seat-'+seat);
                seatView.removeClass('seat-active').removeClass('seat-inactive');
                seatView.addClass( status === 1 ? 'seat-active':'seat-inactive' );
            });

            /* Track events on google maps */
            var location = report.location;
            var time = report.time;
            var passengers = report.passengers;

            if (trackingPoint) {
                trackingPoint.marker.setPosition(new google.maps.LatLng({
                    lat: parseFloat(location.latitude),
                    lng: parseFloat(location.longitude)
                }));
                trackingPoint.infowindow.setContent(makeContentMarkerMain(time,passengers));
                trackingPoint.infowindow.open(map,trackingPoint.marker);
            }

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
            }else{
                setTimeout(function () {
                    stop();
                }, 5000);
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
        var markers = [];
        var trackingPoint = {
            marker:null,
            infowindow:null
        };
        var infowindows = [];
        var bounds = new google.maps.LatLngBounds();

        initializeMap();

        setTimeout(function(){
            seatingStatusReport.forEach(function(report,i){
                var location = report.location;
                var time = report.time;
                var passengers = report.passengers;
                var contentMarker = "<strong><i class='fa fa-clock-o'></i> @lang('Time'):</strong> "+time+"<br><strong><i class='fa fa-users'></i> @lang('Passengers'):</strong> "+passengers+"<br>";

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
                    var contentMarkerMain = makeContentMarkerMain(time, passengers);
                    trackingPoint = addMarker(
                        new google.maps.LatLng({
                            lat: parseFloat(location.latitude),
                            lng: parseFloat(location.longitude)
                        }),
                        contentMarkerMain, null
                    );
                }
            });
            //now fit the map to the newly inclusive bounds
            map.fitBounds(bounds);
        },500);

        // Adds a marker to the map and push to the array.
        function addMarker(location,content,icon) {
            var marker = new google.maps.Marker({
                position: location,
                map: map
            });

            if( icon )marker.setIcon(icon);

            //extend the bounds to include each marker's position
            bounds.extend(marker.position);

            var infowindow = new google.maps.InfoWindow({
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

        function makeContentMarkerMain(time,passengers){
            return "<strong><i class='fa fa-clock-o'></i> @lang('Time'):</strong> " + time + "<br><strong><i class='fa fa-users'></i> @lang('Passengers'):</strong> " + passengers + "<br>";
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