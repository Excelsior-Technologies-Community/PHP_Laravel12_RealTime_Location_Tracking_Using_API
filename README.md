# PHP_Laravel12_RealTime_Location_Tracking_Using_API

## Project Overview

This project is a simple Real‑Time Location Tracking system built using Laravel 12.
It allows a user to send their current GPS latitude and longitude to the server through an API and display the latest location live on Google Maps.

The system uses Browser Geolocation API, Laravel REST APIs, AJAX polling, and Google Maps JavaScript API.

This project is designed for beginners who want to understand how real‑time location tracking works using Laravel.

---

## Features

* Store user latitude and longitude in database
* REST API for saving and fetching location
* Live map updates every few seconds
* Simple and beginner‑friendly code
* Google Maps integration
* No authentication required
* Easy to extend for multiple users

---

## Technology Stack

* PHP 8 or higher
* Laravel 12
* MySQL
* JavaScript
* Google Maps JavaScript API
* Browser Geolocation API
* AJAX / Fetch API

---

## Project Structure

* Model: Location
* Controller: LocationController
* Migration: create_locations_table
* Routes: api.php and web.php
* View: map.blade.php

---

## Installation Steps

### 1. Clone Repository

```bash
git clone https://github.com/your-username/realtime-location.git
cd realtime-location
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment Setup

Copy `.env.example` to `.env`

```bash
cp .env.example .env
```

Update database credentials in `.env` file:

```
DB_DATABASE=realtime_location
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Create Database

Create a new database in phpMyAdmin or MySQL:

```
realtime_location
```

### 6. Run Migrations

```bash
php artisan migrate
```

### 7. Start Server

```bash
php artisan serve
```

Open browser:

```
http://127.0.0.1:8000
```
<img width="1918" height="641" alt="image" src="https://github.com/user-attachments/assets/6e41c553-eb40-4252-837e-99e1ace180d9" />

<img width="1384" height="819" alt="image" src="https://github.com/user-attachments/assets/4c5055d0-dd36-4e91-ae0b-c801aac190b8" />


---

## Google Maps API Setup

1. Go to Google Cloud Console
2. Create a new project
3. Enable "Maps JavaScript API"
4. Generate API Key
5. Replace API key in `map.blade.php`

Example:

```html
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY"></script>
```

---

## API Endpoints

### Save Location

Method: POST
URL: `/api/save-location`

Body Example:

```json
{
  "user_name": "Mihir",
  "latitude": 23.0225,
  "longitude": 72.5714
}
```

### Get Latest Location

Method: GET
URL: `/api/latest-location`

---

## How It Works

1. Browser asks for location permission
2. Latitude and longitude are captured
3. Data is sent to Laravel API
4. Location is stored in MySQL database
5. Frontend fetches latest location every few seconds
6. Map marker updates automatically

---

## Database Table Structure

Table: locations

Columns:

* id
* user_name
* latitude
* longitude
* created_at
* updated_at

---

## Use Cases

* Delivery Tracking
* Employee Monitoring
* Vehicle Tracking
* Student Projects
* Learning Real‑Time APIs

---

## Future Improvements

* Multi‑User Tracking
* Authentication System
* WebSocket Real‑Time Updates
* Route History Display
* Mobile App Integration
* Admin Dashboard

---

## Requirements

* PHP 8 or higher
* Composer
* MySQL
* Internet Connection
* Google Maps API Key

---

## Author

Mihir Mehta

---

## License

This project is open‑source and available under the MIT License.

