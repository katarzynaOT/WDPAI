<?php

require_once 'Routing.php'; 

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    // cookie PRZED session_start()
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_secure', '0'); // 0 dla localhost, 1 dla HTTPS
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1'); 
    ini_set('session.cookie_lifetime', 86400); // 24h
    
    session_start();
}

$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// DEFAULT
Router::get('', 'DefaultController@index');


// AUTH
Router::get('login', 'AuthorizationController@showLogin');
Router::post('login', 'AuthorizationController@login');

Router::get('register', 'AuthorizationController@showRegister');

Router::get('register/student', 'AuthorizationController@showStudentRegister');
Router::get('register/tutor', 'AuthorizationController@showTutorRegister');

Router::post('register/student', 'AuthorizationController@registerStudent');
Router::post('register/tutor', 'AuthorizationController@registerTutor');

Router::get('logout', 'AuthorizationController@logout');

// SEARCH / MATCHING
#Router::get('search', 'TutorController@search');
#Router::post('search', 'TutorController@searchResults');

// COMMENTS
#Router::post('comment/add', 'TutorController@addComment');

// DASHBOARD
Router::get('dashboard', 'dashboard/StudentDashboardController@index');
Router::get('student/dashboard', 'dashboard/StudentDashboardController@index');
Router::get('tutor/dashboard', 'dashboard/TutorDashboardController@index');

// PROFILE
Router::get('student/profile', 'profile/StudentProfileController@edit');
Router::post('student/profile/update', 'profile/StudentProfileController@update');

Router::get('tutor/profile', 'profile/TutorProfileController@edit');
Router::post('tutor/profile/update', 'profile/TutorProfileController@update');

// TUTOR BROWSING
Router::get('student/tutors', 'TutorBrowsingController@list');
Router::get('tutor/:id', 'TutorBrowsingController@profile');

// BOOKING
Router::get('booking/new', 'BookingController@create');
Router::post('booking/store', 'BookingController@store');
Router::get('student/bookings', 'BookingController@listStudentBookings');
Router::get('tutor/bookings', 'BookingController@listTutorBookings');
Router::post('booking/confirm', 'BookingController@confirm');
Router::post('booking/cancel', 'BookingController@cancel');

// RUN
Router::run($path);




