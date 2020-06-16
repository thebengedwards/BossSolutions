$(document).ready(function(){
    $.ajax({
        method: "GET",
        type: "json",
        url: "mapCall.php"
    }).done(function (response) {
        var responseJSON = JSON.parse(response);

            findCoordinates(responseJSON[0].topic_address);
            console.log(responseJSON[0].topic_address);
    })

})
