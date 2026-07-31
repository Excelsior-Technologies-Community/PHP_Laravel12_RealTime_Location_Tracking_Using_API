<!DOCTYPE html>
<html>

<head>
    <title>Realtime Location Tracking</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAP_KEY"></script>
</head>

<body>

    <h2>Realtime Location Tracking</h2>

    <p id="status">
        Waiting for location...
    </p>

    <div id="map" style="height:500px;width:100%;"></div>

    <script>
        let map, marker;

        function initMap(lat = 23.0225, lng = 72.5714) {
            let myLatLng = {
                lat: lat,
                lng: lng
            };

            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 15,
                center: myLatLng,
            });

            marker = new google.maps.Marker({

                position: myLatLng,

                map: map,

                title: "Current User Location",

                animation: google.maps.Animation.DROP

            });
        }

        // Get Browser Location
        function sendLocation() {

            if (!navigator.geolocation) {
                document.getElementById("status").innerHTML =
                    "❌ Geolocation is not supported.";
                return;
            }

            navigator.geolocation.getCurrentPosition(function(position) {

                let lat = position.coords.latitude;
                let lng = position.coords.longitude;

                document.getElementById("status").innerHTML =
                    "📍 Sending location...";

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

                    })

                    .then(res => res.json())

                    .then(data => {

                        document.getElementById("status").innerHTML =
                            "✅ Location Updated";

                    })

                    .catch(() => {

                        document.getElementById("status").innerHTML =
                            "❌ Failed to send location.";

                    });

            });

        }

        // Fetch Latest Location
        function fetchLocation() {

            fetch('/api/latest-location')

                .then(res => res.json())

                .then(data => {

                    if (!data || !data.latitude) {
                        return;
                    }

                    let pos = {

                        lat: parseFloat(data.latitude),
                        lng: parseFloat(data.longitude)

                    };

                    marker.setPosition(pos);

                    map.setCenter(pos);

                })

                .catch(error => {

                    console.log(error);

                });

        }

        initMap();
        setInterval(sendLocation, 15000);
        setInterval(fetchLocation, 15000);
    </script>

</body>

</html>