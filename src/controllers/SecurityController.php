<?php

require_once 'AppController.php'; // załączenie AppControllera
class SecurityController extends AppController {

    // TODO dekarator, który definiuje, jakie metody HTTP są dostępne
    public function login() {
        // TODO
        // pobieramy z formularza email, haslo
        // jesli nie istnieje zwracamy odpowiednie komunikaty
        // jesli istnieje przekierowujemy na dashboard

        //return $this->render("login");
        //return $this->render("login", ['messages' => 'blad logowania']);

        //if($this->isGet()) {
        //    return $this->render("login");
        //} 
        if($this->isPost()) {
            return $this->render("login");
        } 
        //var_dump(IS_POST);

        if ($email === ' ') {
            return $this->render("login", ["message" => "Podaj email"]);
        }

        $email = $_POST["email"] ?? '';
        $password = $_POST["password"] ?? '';
        var_dump($email, $password);
        $this->render("dashboard");

    }

    public function register() {
        // TODO pobranie z formularza email i hasła
        // TODO insert do bazy danych
        // TODO zwrocenie informajci o pomyslnym zarejstrowaniu

        //return $this->render("register");

        if ($this->isGet()) {
            return $this->render("register");
        }

        if($this->isPost()) {
            return $this->render("register");
        }

        $email = $_POST["email"] ?? '';
        $password1 = $_POST["password1"] ?? '';
        $password2 = $_POST["password2"] ?? '';
        $firstname = $_POST["firstname"] ?? '';
        $lastname = $_POST["lastname"] ?? '';

        return $this->render("login", ["message" => "Zarejestrowano uytkownika ".$email]);

    }
}