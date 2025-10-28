<?php

require_once 'AppController.php'; // załączenie AppControllera
class SecurityController extends AppController {

    public function login() {
        // TODO
        // pobieramy z formularza email, haslo
        // jesli nie istnieje zwracamy odpowiednie komunikaty
        // jesli istnieje przekierowujemy na dashboard

        //return $this->render("login");
        return $this->render("login", ['messages' => 'blad logowania']);
    }

    public function register() {

        return $this->render("register");
    }
}