<?php

class Database {
    private static $host;
    private static $db_name;
    private static $username;
    private static $password;
    private static $conn = null;

    public static function getConnection() {
        if (self::$conn === null) {
            self::loadEnv();
            
            try {
                $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$db_name . ";charset=utf8mb4";
                self::$conn = new PDO($dsn, self::$username, self::$password);
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                self::$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            } catch(PDOException $exception) {
                // En producción, no mostrar el error detallado al usuario
                error_log("Connection error: " . $exception->getMessage());
                die("Error de conexión a la base de datos. Por favor revise los logs.");
            }
        }
        return self::$conn;
    }

    private static function loadEnv() {
        // Carga simple de .env para entorno nativo sin librerías externas
        $envFile = __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);
                putenv(sprintf('%s=%s', $name, $value)); // Opcional, para compatibilidad
                $_ENV[$name] = $value;
            }
        }

        self::$host = $_ENV['DB_HOST'] ?? 'localhost';
        self::$db_name = $_ENV['DB_NAME'] ?? '';
        self::$username = $_ENV['DB_USER'] ?? 'root';
        self::$password = $_ENV['DB_PASS'] ?? '';
    }
}
