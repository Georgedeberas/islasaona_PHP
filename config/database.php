<?php

class Database
{
    // Credenciales directas para asegurar conexión en hosting compartido
    // sin depender de variables de entorno si la carga falla.
    private static $host = 'nexosystem.yourwebhostingmysql.com';
    private static $db_name = 'mochilerosrd_islasaona';
    private static $username = 'islasaona';
    private static $password = 'Islasaonaervi123456';
    private static $conn = null;

    public static function getConnection()
    {
        if (self::$conn === null) {
            try {
                // Intentar cargar .env si existe para sobreescribir (desarrollo local)
                self::loadEnv();

                $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$db_name . ";charset=utf8mb4";
                self::$conn = new PDO($dsn, self::$username, self::$password);
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                self::$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            } catch (PDOException $exception) {
                // Loguear error real
                error_log("Connection error: " . $exception->getMessage());

                // Si estamos en producción, intentar un mensaje más amigable pero diagnóstico si se solicita
                // IMPORTANTE: En hosting compartido, a veces el "localhost" es requerido en vez de la IP externa
                // Si falla con el host externo, intentar localhost como fallback automático
                if (strpos($exception->getMessage(), 'host') !== false) {
                    try {
                        $dsnLocal = "mysql:host=localhost;dbname=" . self::$db_name . ";charset=utf8mb4";
                        self::$conn = new PDO($dsnLocal, self::$username, self::$password);
                        self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        return self::$conn;
                    } catch (PDOException $e2) {
                        die("Error Crítico: No se pudo conectar a la base de datos. Verifique credenciales. " . $e2->getMessage());
                    }
                }

                die("Error de conexión a la base de datos: " . $exception->getMessage());
            }
        }
        return self::$conn;
    }

    private static function loadEnv()
    {
        $envFile = __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0)
                    continue;
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);

                // Sobrescribir estáticos si existen en .env
                if ($name === 'DB_HOST')
                    self::$host = $value;
                if ($name === 'DB_NAME')
                    self::$db_name = $value;
                if ($name === 'DB_USER')
                    self::$username = $value;
                if ($name === 'DB_PASS')
                    self::$password = $value;
            }
        }
    }
}
