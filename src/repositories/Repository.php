<?php

require_once __DIR__.'/../Database.php';

class Repository {
    //protected $database;
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection(); // DB Singleton
    }

    // Ochrona przed SQL Injection
    protected function escapeLike(string $value): string
    {
        return addcslashes($value, '%_\\');
    }

}