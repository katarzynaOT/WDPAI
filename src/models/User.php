<?php

class User {
    public int $id;
    public string $email;
    public string $passwordHash;
    public string $role;
    public ?string $firstName;
    public ?string $lastName;
    public ?string $phone;
    public ?float $hourlyRate;
    public ?string $createdAt;
    public ?string $lastLogin;
    

    /*public function __construct(
        string $email,
        string $password,
        string $name,
        string $surname,
        int $id = null
    ) {
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        $this->surname = $surname;
        $this->id = $id;
    }*/ 

    //Gettery
}
