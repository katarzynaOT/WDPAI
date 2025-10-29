<?php

require_once 'src/controllers/SecurityController.php';
require_once 'src/controllers/DashboardController.php';



class Routing {

    public static $routes = [ //statyczna tablica do powiazania sciezek z kontrolerami
        // TODO - wymyslic system, ktory by przekierowywal np. /dashboard/123, nie tylko /dashboard -> zrobic to przez $path->REGEX
        'login' => [
            'controller' => 'SecurityController',
            'action' => 'login'
        ],
        'register' => [
            'controller' => 'SecurityController',
            'action' => 'register'
        ],
        'dashboard' => [
            'controller' => 'DashboardController',
            'action' => 'index'
        ]
    ];

    public static function run(string $path) {
        switch ($path) {        //na podstawie sciezki sprawdzamy jaki HTML zwrocic
            // TODO: singleton tu uzyc
            case 'dashboard':
                // TODO connect with database
                // get elements to present on dashboard

                include 'public/views/dashboard.html';
                break;
            case 'login':
                //include 'public/views/login.html';

                //$controller = new SecurityController(); // by nie tworzyc nowego obiektu za kazdym requestem to wykorzystujemy Singleton
                //$controller->login();
                //break;
            case 'register':
                $controller = Routing::$routes[$path]["controller"];
                $action = Routing::$routes[$path]["action"];
                //$controller = self::$routes[$path]["controller"];
                //$action = self::$routes[$path]["action"];

                $controllerObj = new $controller;
                $controllerObj->$action();
                break; 
            default:
                include 'public/views/404.html';
                break;
        }
    }
}