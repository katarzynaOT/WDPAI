<?php

require_once 'AppController.php';

class DefaultController extends AppController
{
    public function index(): void
    {
        echo 'DEFAULT CONTROLLER INDEX - aa';
        $this->render('home');
    }
}
