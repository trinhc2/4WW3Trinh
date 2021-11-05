function getLocation() {
    //If geolocation api is enabled
    if (navigator.geolocation) {
        //Ask user for their location, callback to success with position object if succesful
        navigator.geolocation.getCurrentPosition(success);
    }
}

//On Success reroute page to results with coordinates in the url (simulating GET request)
function success(position) {
    window.location.replace("./results_sample.html?xcoord=" + position.coords.latitude + "&ycoord=" + position.coords.longitude);
}