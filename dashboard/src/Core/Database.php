<?php

namespace Dashboard\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $host = getenv('DB_HOST') ?: 'mysql';
            $port = getenv('DB_PORT') ?: '3306';
            $name = getenv('DB_NAME') ?: 'tasklogger';
            $user = getenv('DB_USER') ?: 'tasklogger';
            $pass = getenv('DB_PASSWORD') ?: 'tasklogger';

            try {
                self::$instance = new PDO(
                    "mysql:host={$host};port={$port};dbname={$name}",
                    $user,
                    $pass,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
            } catch (PDOException $e) {
                http_response_code(500);
                echo 'Erreur de connexion à la base de données.';
                exit;
            }
        }

        return self::$instance;
    }
}
