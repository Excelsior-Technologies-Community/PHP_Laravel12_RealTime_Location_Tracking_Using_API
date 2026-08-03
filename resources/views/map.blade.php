<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1">

    <title>Laravel 12 Real-Time Location Tracking</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAP_KEY"></script>

    <style>
        body {
            background: #f5f7fb;
        }

        .navbar {
            background: #0d6efd;
        }

        .navbar-brand {
            color: #fff;
            font-weight: bold;
        }

        .navbar-brand:hover {
            color: #fff;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, .1);
        }

        .card-title {
            font-size: 15px;
            color: #666;
        }

        .card-text {
            font-size: 30px;
            font-weight: bold;
        }

        #map {
            width: 100%;
            height: 500px;
            border-radius: 12px;
        }

        .table-responsive {
            max-height: 450px;
            overflow: auto;
        }

        .status-box {
            padding: 12px;
            border-radius: 10px;
            background: #e9ecef;
            font-weight: bold;
        }
    </style>

</head>

<body>

    <nav class="navbar navbar-expand-lg">

        <div class="container">

            <a class="navbar-brand" href="#">

                📍 Laravel 12 Real-Time Location Tracking

            </a>

        </div>

    </nav>

    <div class="container mt-4">

        <div class="row">

            <div class="col-md-3">

                <div class="card text-center">

                    <div class="card-body">

                        <h6 class="card-title">

                            Total Locations

                        </h6>

                        <h2 class="card-text text-primary"

                            id="totalLocations">

                            0

                        </h2>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="card text-center">

                    <div class="card-body">

                        <h6 class="card-title">

                            Total Users

                        </h6>

                        <h2 class="card-text text-success"

                            id="totalUsers">

                            0

                        </h2>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="card text-center">

                    <div class="card-body">

                        <h6 class="card-title">

                            Latest User

                        </h6>

                        <h5 id="latestUser">

                            --

                        </h5>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="card text-center">

                    <div class="card-body">

                        <h6 class="card-title">

                            Last Updated

                        </h6>

                        <h6 id="lastUpdated">

                            --

                        </h6>

                    </div>

                </div>

            </div>

        </div>

        <hr>

        <div class="row mb-3">

            <div class="col-md-3">

                <input

                    type="text"

                    id="searchUser"

                    class="form-control"

                    placeholder="Search User">

            </div>

            <div class="col-md-3">

                <input

                    type="date"

                    id="filterDate"

                    class="form-control">

            </div>

            <div class="col-md-6 text-end">

                <button

                    class="btn btn-primary"

                    id="btnSearch">

                    Search

                </button>

                <button

                    class="btn btn-success"

                    onclick="window.open('/api/export-csv')">

                    Export CSV

                </button>

                <button

                    class="btn btn-danger"

                    id="btnClear">

                    Clear History

                </button>

            </div>

        </div>

        <div class="row">

            <div class="col-md-12">

                <div

                    class="status-box"

                    id="status">

                    Waiting for location...

                </div>

            </div>

        </div>

        <div class="row mt-4">

            <div class="col-lg-8">

                <div class="card">

                    <div class="card-header bg-primary text-white">

                        Google Map

                    </div>

                    <div class="card-body">

                        <div id="map"></div>

                    </div>

                </div>

            </div>

            <div class="col-lg-4">

                <div class="card">

                    <div class="card-header bg-success text-white">

                        Latest Information

                    </div>

                    <div class="card-body">

                        <table class="table">

                            <tr>

                                <th>User</th>

                                <td id="infoUser">--</td>

                            </tr>

                            <tr>

                                <th>Latitude</th>

                                <td id="infoLat">--</td>

                            </tr>

                            <tr>

                                <th>Longitude</th>

                                <td id="infoLng">--</td>

                            </tr>

                            <tr>

                                <th>Tracked At</th>

                                <td id="infoTime">--</td>

                            </tr>

                        </table>

                    </div>

                </div>

            </div>

        </div>

        <div class="row mt-4">

            <div class="col-md-12">

                <div class="card">

                    <div class="card-header bg-dark text-white">

                        Location History

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table

                                class="table table-bordered table-striped">

                                <thead>

                                    <tr>

                                        <th>ID</th>

                                        <th>User</th>

                                        <th>Latitude</th>

                                        <th>Longitude</th>

                                        <th>Tracked At</th>

                                        <th>Action</th>

                                    </tr>

                                </thead>

                                <tbody id="historyTable">

                                    <tr>

                                        <td colspan="6"

                                            class="text-center">

                                            Loading...

                                        </td>

                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>
        <script>
            let map;
            let marker;

            /*
            |--------------------------------------------------------------------------
            | Initialize Google Map
            |--------------------------------------------------------------------------
            */

            function initMap(lat = 23.0225, lng = 72.5714) {
                const position = {
                    lat: lat,
                    lng: lng
                };

                map = new google.maps.Map(document.getElementById("map"), {

                    zoom: 15,

                    center: position,

                    mapTypeId: "roadmap"

                });

                marker = new google.maps.Marker({

                    position: position,

                    map: map,

                    title: "Current User Location",

                    animation: google.maps.Animation.DROP

                });

            }

            /*
            |--------------------------------------------------------------------------
            | Send Browser Location
            |--------------------------------------------------------------------------
            */

            function sendLocation() {

                if (!navigator.geolocation) {

                    document.getElementById("status").innerHTML =
                        "❌ Geolocation is not supported.";

                    return;

                }

                navigator.geolocation.getCurrentPosition(function(position) {

                    let latitude = position.coords.latitude;

                    let longitude = position.coords.longitude;

                    document.getElementById("status").innerHTML =
                        "📡 Sending current location...";

                    fetch('/api/save-location', {

                            method: 'POST',

                            headers: {

                                'Content-Type': 'application/json',

                                'Accept': 'application/json'

                            },

                            body: JSON.stringify({

                                user_name: 'Mihir',

                                latitude: latitude,

                                longitude: longitude

                            })

                        })

                        .then(response => response.json())

                        .then(result => {

                            if (result.success) {

                                document.getElementById("status").innerHTML =
                                    "✅ Location updated successfully.";

                            } else {

                                document.getElementById("status").innerHTML =
                                    "❌ Failed to save location.";

                            }

                        })

                        .catch(error => {

                            console.log(error);

                            document.getElementById("status").innerHTML =
                                "❌ Error while sending location.";

                        });

                });

            }

            /*
            |--------------------------------------------------------------------------
            | Fetch Latest Location
            |--------------------------------------------------------------------------
            */

            function loadLatestLocation() {

                fetch('/api/latest-location')

                    .then(response => response.json())

                    .then(result => {

                        if (!result.success) {
                            return;
                        }

                        let location = result.data;

                        let lat = parseFloat(location.latitude);

                        let lng = parseFloat(location.longitude);

                        let point = {

                            lat: lat,

                            lng: lng

                        };

                        marker.setPosition(point);

                        map.setCenter(point);

                        document.getElementById("infoUser").innerHTML =
                            location.user_name;

                        document.getElementById("infoLat").innerHTML =
                            location.latitude;

                        document.getElementById("infoLng").innerHTML =
                            location.longitude;

                        document.getElementById("infoTime").innerHTML =
                            location.tracked_at;

                    })

                    .catch(error => {

                        console.log(error);

                    });

            }

            /*
            |--------------------------------------------------------------------------
            | Load Dashboard Statistics
            |--------------------------------------------------------------------------
            */

            function loadStatistics() {

                fetch('/api/location-stats')

                    .then(response => response.json())

                    .then(result => {

                        if (!result.success) {
                            return;
                        }

                        let stats = result.statistics;

                        document.getElementById("totalLocations").innerHTML =
                            stats.total_locations;

                        document.getElementById("totalUsers").innerHTML =
                            stats.total_users;

                        document.getElementById("latestUser").innerHTML =
                            stats.latest_user ?? "--";

                        document.getElementById("lastUpdated").innerHTML =
                            stats.last_updated ?? "--";

                    })

                    .catch(error => {

                        console.log(error);

                    });

            }

            /*
            |--------------------------------------------------------------------------
            | Initial Load
            |--------------------------------------------------------------------------
            */

            initMap();

            sendLocation();

            loadLatestLocation();

            loadStatistics();
        </script>

        <script>
            /*
|--------------------------------------------------------------------------
| Load Location History
|--------------------------------------------------------------------------
*/

            function loadHistory(url = '/api/location-history') {
                fetch(url)

                    .then(response => response.json())

                    .then(result => {

                        if (!result.success) {
                            return;
                        }

                        let rows = "";

                        result.data.data.forEach(function(location) {

                            rows += `
                <tr>

                    <td>${location.id}</td>

                    <td>${location.user_name}</td>

                    <td>${location.latitude}</td>

                    <td>${location.longitude}</td>

                    <td>${location.tracked_at}</td>

                    <td>

                        <button
                            class="btn btn-danger btn-sm"
                            onclick="deleteLocation(${location.id})">

                            Delete

                        </button>

                    </td>

                </tr>
            `;

                        });

                        document.getElementById("historyTable").innerHTML = rows;

                    })

                    .catch(error => console.log(error));

            }

            /*
            |--------------------------------------------------------------------------
            | Search User
            |--------------------------------------------------------------------------
            */

            document.getElementById("btnSearch").addEventListener("click", function() {

                let user = document.getElementById("searchUser").value;

                if (user == "") {
                    loadHistory();
                    return;
                }

                loadHistory('/api/search-location?user_name=' + encodeURIComponent(user));

            });


            /*
            |--------------------------------------------------------------------------
            | Filter By Date
            |--------------------------------------------------------------------------
            */

            document.getElementById("filterDate").addEventListener("change", function() {

                let date = this.value;

                if (date == "") {
                    loadHistory();
                    return;
                }

                loadHistory('/api/filter-date?date=' + date);

            });


            /*
            |--------------------------------------------------------------------------
            | Delete Location
            |--------------------------------------------------------------------------
            */

            function deleteLocation(id) {

                if (!confirm("Delete this location?")) {
                    return;
                }

                fetch('/api/location/' + id, {

                        method: "DELETE"

                    })

                    .then(response => response.json())

                    .then(result => {

                        alert(result.message);

                        loadHistory();

                        loadStatistics();

                    });

            }


            /*
            |--------------------------------------------------------------------------
            | Clear History
            |--------------------------------------------------------------------------
            */

            document.getElementById("btnClear").addEventListener("click", function() {

                if (!confirm("Delete all location history?")) {
                    return;
                }

                fetch('/api/locations/clear', {

                        method: "DELETE"

                    })

                    .then(response => response.json())

                    .then(result => {

                        alert(result.message);

                        loadHistory();

                        loadStatistics();

                    });

            });


            /*
            |--------------------------------------------------------------------------
            | Auto Refresh
            |--------------------------------------------------------------------------
            */

            loadHistory();

            setInterval(function() {

                sendLocation();

                loadLatestLocation();

                loadStatistics();

                loadHistory();

            }, 15000);
        </script>

</body>

</html>