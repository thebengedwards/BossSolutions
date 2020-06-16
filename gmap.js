//Declare the map
var map;
// Initialise and add the map
function initMap() {
    //coordinates of Newcastle
    var uk = {
        lat: 54.9783, lng: -1.6178
    };

    //Map cenetered at Newcastle
    map = new google.maps.Map(
        document.getElementById('map'), {
            //Map settings
            // Set the zoom and center on the newcastle coordinates
            zoom: 5,
            center: uk
        }
    );

    //declaring the geocoder
    geocoder = new google.maps.Geocoder();
}


function createMarker(long, lat){
    markerLatLng = new google.maps.LatLng(lat, long);
    marker = new google.maps.Marker({
        position: markerLatLng,
        map: map,
    });

    marker.setMap(map);
}