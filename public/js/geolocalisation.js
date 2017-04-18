
function maPosition(position){
    document.getElementById('lat').value = position.coords.latitude;
    document.getElementById('lon').value = position.coords.longitude;
}

if (navigator.geolocation){
    navigator.geolocation.getCurrentPosition(maPosition);
}