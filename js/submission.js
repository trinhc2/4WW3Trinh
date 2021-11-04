var xCord = document.getElementById("xcoord");
var yCord = document.getElementById("ycoord");
function getLocation() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(showPosition);
  }
}

function showPosition(position) {
    document.getElementById("xcoord").value = position.coords.latitude;
    document.getElementById("ycoord").value = position.coords.longitude;
}