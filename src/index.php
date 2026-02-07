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
Router::get('', 'AuthorizationController@showLogin');


// AUTH
Router::get('login', 'AuthorizationController@showLogin');
Router::post('login', 'AuthorizationController@login');

Router::get('register', 'AuthorizationController@showRegister');
Router::post('register', 'AuthorizationController@register');

Router::get('logout', 'AuthorizationController@logout');

// DASHBOARD
Router::get('student/dashboard', 'profile/StudentProfileController@dashboard');
Router::get('tutor/dashboard', 'profile/TutorProfileController@dashboard');

// PROFILE'
Router::get('student/profile', 'profile/StudentProfileController@showHtml');
Router::get('tutor/profile', 'profile/TutorProfileController@showHtml');

Router::get('student/profile/data', 'profile/StudentProfileController@showData'); //json
Router::get('tutor/profile/data', 'profile/TutorProfileController@showData');

Router::get('student/profile/edit', 'profile/StudentProfileController@edit');
Router::get('tutor/profile/edit', 'profile/TutorProfileController@edit');

Router::post('student/profile/update', 'profile/StudentProfileController@update');
Router::post('tutor/profile/update', 'profile/TutorProfileController@update');

// TUTOR BROWSING
Router::get('tutors', 'TutorBrowsingController@list');
Router::get('tutor/:id', 'TutorBrowsingController@profile');

// REVIEWS
Router::get('tutor/:id/review', 'ReviewController@create');
Router::post('tutor/:id/review/store', 'ReviewController@store');

// SUBJECTS
Router::get('subjects/list', 'SubjectController@list');

// LESSONS
Router::get('lessons', 'LessonController@listHtml');
Router::get('lessons/:id', 'LessonController@detailsHtml');

// API endpoint do listy lekcji
Router::get('lessons/data', 'LessonController@lessonsData');

Router::get('student/lesson/:id/edit-details', 'LessonController@editDetailsStudent');
Router::get('tutor/lesson/:id/edit-details', 'LessonController@editDetailsTutor');


Router::get('booking/new', 'BookingController@create');
Router::post('booking/store', 'BookingController@store');

Router::get('bookings', 'BookingController@listBookings'); 

Router::post('booking/confirm', 'BookingController@confirm');
Router::post('booking/cancel', 'BookingController@cancel');

Router::get('lesson/:id', 'LessonController@show');
Router::get('lesson/:id/edit', 'LessonController@edit');

Router::post('lesson/:id/update', 'LessonController@update');

Router::get('lesson/:id/homework', 'LessonController@homework');
Router::post('lesson/:id/homework/store', 'LessonController@storeHomework');

// ERROR
//Router::get('error/405', 'ErrorController@show405');

// RUN
Router::run($path);




