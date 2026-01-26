<?php

require_once 'AppController.php';

class DefaultController extends AppController
{
    public function index(): void
    {
        echo 'Routing działa!';
        $this->render('home');
    }
}
