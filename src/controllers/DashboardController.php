<?php

require_once 'AppController.php'; // załączenie AppControllera
class DashboardController extends AppController {

    public function index() {
        // TODO

        return $this->render("dashboard");
    }
}