<?php

require_once "config.php";
//TODO config zamienic na .env

class Database {
    private $username; //stale -> teraz są w config.php
    private $password;
    private $host;
    private $database;

    public function __construct()
    {
        $this->username = USERNAME; //w klasie -> singletonie
        $this->password = PASSWORD;
        $this->host = HOST;
        $this->database = DATABASE;
    }

    public function connect() //TODO dorobic metode disconnect (skoro jest juz connect)
    {
        try {
            $conn = new PDO( //klasa PDO - PHP Data Objects -> wbudowana biblioteka (trzeba importowac -juz zrobione w czesniej)
                "pgsql:host=$this->host;port=5432;dbname=$this->database",
                $this->username,
                $this->password,
                ["sslmode"  => "prefer"]
            );

            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        }
        catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
}