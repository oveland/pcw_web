{{-- /**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 24/05/2017
 * Time: 2:49 PM
 */
 --}}
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('road.google_api_token') }}" async defer></script>
<script type="text/javascript">
    var map;
    var routeLayerKML;
    var mapLightDreamStyles = [
        {
            "featureType": "landscape",
            "stylers": [{"hue": "#FFBB00"}, {"saturation": 43.400000000000006}, {"lightness": 37.599999999999994}, {"gamma": 1}]
        },
        {
            "featureType": "road.highway",
            "stylers": [{"hue": "#FFC200"}, {"saturation": -61.8}, {"lightness": 45.599999999999994}, {"gamma": 1}]
        },
        {
            "featureType": "road.arterial",
            "stylers": [{"hue": "#FF0300"}, {"saturation": -100}, {"lightness": 51.19999999999999}, {"gamma": 1}]
        },
        {
            "featureType": "road.local",
            "stylers": [{"hue": "#FF0300"}, {"saturation": -100}, {"lightness": 52}, {"gamma": 1}]
        },
        {
            "featureType": "water",
            "stylers": [{"hue": "#005aa6"}, {"saturation": -13.200000000000003}, {"lightness": 2.4000000000000057}, {"gamma": 1}]
        },
        {
            "featureType": "poi",
            "stylers": [{"hue": "#80b422"}, {"saturation": -1.0989010989011234}, {"lightness": 11.200000000000017}, {"gamma": 1}]
        }
    ];

    function initializeMap() {
        setTimeout(function(){
            var mapOptions = {
                zoom: 14,
                center: new google.maps.LatLng(3.455608, -76.58943),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            map = new google.maps.Map(document.getElementById('google-map-light-dream'), mapOptions);
            map.setOptions({styles: mapLightDreamStyles});
            routeLayerKML = new google.maps.KmlLayer();
        },500);
    }
</script>
