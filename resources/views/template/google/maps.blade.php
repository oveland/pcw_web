{{-- /**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 24/05/2017
 * Time: 2:49 PM
 */
 --}}

<script src="https://maps.googleapis.com/maps/api/js?key={{ config('road.google_api_token') }}" async defer></script>

<script type="text/javascript">
    let map = null;
    let routeLayerKML;
    let trafficLayer;
    let mapLightDreamStyles = [
        {
            featureType: "landscape",
            stylers: [{"hue": "#8e9293"}, {"saturation": 43.400000000000006}, {"lightness": 37.599999999999994}, {"gamma": 1}]
        },
        {
            featureType: "road.highway",
            stylers: [{"hue": "#FFC200"}, {"saturation": -61.8}, {"lightness": 45.599999999999994}, {"gamma": 1}]
        },
        {
            featureType: "road.arterial",
            stylers: [{"hue": "#fff80f"}, {"saturation": -100}, {"lightness": 51.19999999999999}, {"gamma": 1}]
        },
        {
            featureType: "road.local",
            stylers: [{"hue": "#00818c"}, {"saturation": -100}, {"lightness": 52}, {"gamma": 1}]
        },
        {
            featureType: "water",
            stylers: [{"hue": "#005aa6"}, {"saturation": -13.200000000000003}, {"lightness": 2.4000000000000057}, {"gamma": 1}]
        },
        {
            featureType: "poi",
            stylers: [{"hue": "#28f900"}, {"saturation": -1.0989010989011234}, {"lightness": 11.200000000000017}, {"gamma": 1}]
        },
        {
            featureType: 'poi.business',
            stylers: [{visibility: 'off'}]
        },
        {
            featureType: 'transit',
            elementType: 'labels.icon',
            stylers: [{visibility: 'off'}]
        }
    ];

    function destroyMap(){
        if(map && google){
            google.maps.event.clearInstanceListeners(window);
            google.maps.event.clearInstanceListeners(document);
        }
    }

    function initializeMap(callback) {
        destroyMap();

        let windowHeight = window.innerHeight - 40;
        $('#modal-route-report .modal-dialog').css('height', windowHeight+"px");
        $('#google-map-light-dream').html('').empty().css('height', ((windowHeight - 30 - $('#modal-route-report .modal-header').height())+"px"));

        setTimeout(function(){
            let mapOptions = {
                zoom: 14,
                center: new google.maps.LatLng(
                    3.445951192812193, -76.52618682268542
                ),
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                mapTypeControl: false
            };

            map = new google.maps.Map(document.getElementById('google-map-light-dream'), mapOptions);
            // map.setOptions({styles: mapLightDreamStyles});
            routeLayerKML = new google.maps.KmlLayer();
            trafficLayer = new google.maps.TrafficLayer();

            //var styleControl = document.getElementById('info-route');
            //map.controls[google.maps.ControlPosition.TOP_LEFT].push(styleControl);

            if(callback)callback();
        },300);
    }
</script>
