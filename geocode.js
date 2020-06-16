function findCoordinates(location) {
    //Grabs the location, name and text parameters from other javascript file
    geocoder.geocode({'address': location},
        function (results, status) {
            //Creates marker on the map based on geocoder results of location
            if (status == google.maps.GeocoderStatus.OK) {
                createMarker(results[0].geometry.location.lng(), results[0].geometry.location.lat());

                //Creates an info window on the marker on the map, with address
                infoWindow = new google.maps.InfoWindow();
                marker.addListener('mouseover', function() {
                    infoWindow.setContent( "Address : " + location);
                    infoWindow.open(map, this);
                });

                //Closes the window when the mouse is not hovering over the marker
                marker.addListener('mouseout', function(){
                    infoWindow.close();
                });

            }
            else {
                //Print error message with status message if fails
                alert('Geocode failed, reason: ' + status);
            }
        }

    );
}
