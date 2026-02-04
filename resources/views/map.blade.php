<!DOCTYPE html>
<html>
<head>
    <title>Realtime Location Tracking</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAG51y1x54XuveIKH4OEcns_v2ZvVJqJfY"></script>
</head>
<body>

<h2>Realtime Location Tracking</h2>
<div id="map" style="height:500px;width:100%;"></div>

<script>
let map, marker;

function initMap(lat = 23.0225, lng = 72.5714) {
    let myLatLng = { lat: lat, lng: lng };

    map = new google.maps.Map(document.getElementById("map"), {
        zoom: 15,
        center: myLatLng,
    });

    marker = new google.maps.Marker({
        position: myLatLng,
        map: map,
    });
}

// Get Browser Location
function sendLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {

            let lat = position.coords.latitude;
            let lng = position.coords.longitude;

            fetch('/api/save-location', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    user_name: 'Mihir',
                    latitude: lat,
                    longitude: lng
                })
            });

        });
    }
}

// Fetch Latest Location
function fetchLocation() {
    fetch('/api/latest-location')
    .then(res => res.json())
    .then(data => {
        if(data) {
            let pos = { lat: parseFloat(data.latitude), lng: parseFloat(data.longitude) };
            marker.setPosition(pos);
            map.setCenter(pos);
        }
    });
}

initMap();
setInterval(sendLocation, 5000);   // send every 5 sec
setInterval(fetchLocation, 5000);  // fetch every 5 sec
</script>

</body>
</html>
