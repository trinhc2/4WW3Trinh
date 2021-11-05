function getLocation() {
  //if geolocation api is available
  if (navigator.geolocation) {
    //prompt user for their location
    navigator.geolocation.getCurrentPosition(showPosition);
  }
}

//Update the form values to the received coordinates
function showPosition(position) {
    document.getElementById("xcoord").value = position.coords.latitude;
    document.getElementById("ycoord").value = position.coords.longitude;
}