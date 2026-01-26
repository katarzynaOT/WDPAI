<?php

require_once 'Routing.php'; #na razie ręczny - bez composera
#require_once 'env.php';

$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Jeśli pracujesz w podkatalogu, usuń część ścieżki
#$basePath = ''; 
#if ($basePath && strpos($path, $basePath) === 0) {
#    $path = substr($path, strlen($basePath));
#    $path = ltrim($path, '/');
#}

// DEFAULT
Router::get('', 'DefaultController@index');

// AUTH
#Router::get('login', 'SecurityController@showLogin');
#Router::post('login', 'SecurityController@login');
#Router::get('register', 'SecurityController@showRegister');
#Router::post('register', 'SecurityController@register');
#outer::get('logout', 'SecurityController@logout');

// SEARCH / MATCHING
#Router::get('search', 'TutorController@search');
#Router::post('search', 'TutorController@searchResults');

// COMMENTS
#Router::post('comment/add', 'TutorController@addComment');

// DASHBOARD
#Router::get('dashboard', 'StudentController@dashboard');

// RUN
Router::run($path);




