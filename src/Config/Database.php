<?php

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private static $conn = null;

    public static function getConnection()
    {
        if (self::$conn === null) {

            // Cargar configuración nativa (PHP Array)
            $configPath = __DIR__ . '/config.php';
            if (!file_exists($configPath)) {
                die("<h1>Error de Configuración</h1><p>Archivo config.php no encontrado.</p>");
            }
            $config = require $configPath;

            $host = $config['DB_HOST'] ?? 'localhost';
            $db_name = $config['DB_NAME'] ?? '';
            $username = $config['DB_USER'] ?? '';
            $password = $config['DB_PASS'] ?? '';

            try {
                $dsn = "mysql:host=" . $host . ";dbname=" . $db_name . ";charset=utf8mb4";

                self::$conn = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]);

            } catch (PDOException $exception) {
                // Log error privately
                error_log("DB Connection Error: " . $exception->getMessage());
                // Generic error for user
                die("<h1>500 Internal Server Error</h1><p>Database connection failed.</p>");
            }
        }
        return self::$conn;
    }
}
