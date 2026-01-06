<?php

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private static $host = 'nexosystem.yourwebhostingmysql.com';
    private static $db_name = 'mochilerosrd_islasaona';
    private static $username = 'islasaona';
    private static $password = 'Islasaonaervi123456';
    private static $conn = null;

    public static function getConnection()
    {
        if (self::$conn === null) {
            try {
                // Configurar DSN
                $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$db_name . ";charset=utf8mb4";

                // Instanciar PDO con opciones robustas
                self::$conn = new PDO($dsn, self::$username, self::$password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Lanzar excepciones reales
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]);

            } catch (PDOException $exception) {
                // Si falla la conexión principal, intentar Localhost (Fallback común en Shared Hosting)
                if (strpos($exception->getMessage(), 'host') !== false) {
                    try {
                        $dsnLocal = "mysql:host=localhost;dbname=" . self::$db_name . ";charset=utf8mb4";
                        self::$conn = new PDO($dsnLocal, self::$username, self::$password, [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                        ]);
                        return self::$conn;
                    } catch (PDOException $e2) {
                        // Error fatal visible para debug inmediato
                        die("<h1>Error de Base de Datos (Fatal)</h1><p>No se pudo conectar ni remoto ni local. " . $e2->getMessage() . "</p>");
                    }
                }

                die("<h1>Error de Base de Datos</h1><p>" . $exception->getMessage() . "</p>");
            }
        }
        return self::$conn;
    }
}
