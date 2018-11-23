{{-- /**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 24/05/2017
 * Time: 2:49 PM
 */
 --}}

<script src="https://maps.googleapis.com/maps/api/js?key={{ config('road.google_api_token') }}" async defer></script>

<script type="text/javascript">
    let map;
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

    function initializeMap() {
        setTimeout(function(){
            let mapOptions = {
                zoom: 14,
                center: new google.maps.LatLng(
                    3.445951192812193, -76.52618682268542
                ),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            map = new google.maps.Map(document.getElementById('google-map-light-dream'), mapOptions);
            map.setOptions({styles: mapLightDreamStyles});
            map.setOptions({styles: styles['hide']});
            routeLayerKML = new google.maps.KmlLayer();
            trafficLayer = new google.maps.TrafficLayer();
        },300);
    }
</script>
