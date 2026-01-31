<?php

//use PDO;
//use PDOException;

//require_once '/../config.env';

class Database
{
    private static ?PDO $connection = null; // Singleton PDO connection

    private function __construct() { }

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            try {
                self::$connection = new PDO( // Create real PDO connection
                    sprintf(
                        'pgsql:host=%s;
                         port=%s;
                         dbname=%s', // DNS connection string
                        $_ENV['DB_HOST'],
                        $_ENV['DB_PORT'],
                        $_ENV['DB_NAME']
                    ),
                    $_ENV['DB_USER'],
                    $_ENV['DB_PASS'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // PDO options
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
                //echo "Connected to database successfully!<br>";
            } catch (PDOException $e) {
                die('Database connection error: ' . $e->getMessage());
            }
        }

        return self::$connection;
    }
}
