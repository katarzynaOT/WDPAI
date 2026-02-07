<?php

require_once __DIR__ . '/../AppController.php';
require_once __DIR__ . '/../../services/ProfileService.php';

class ProfileController extends AppController
{
    protected ProfileService $profileService; 
    
    public function __construct()
    {
        $this->profileService = new ProfileService(); 
    }

}
