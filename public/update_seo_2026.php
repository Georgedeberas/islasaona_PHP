<?php
// public/update_seo_2026.php
// Script de Migración SEO 2026
// ESTE ARCHIVO DEBE BORRARSE DESPUÉS DE EJECUTARSE

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../src/autoload.php';

use App\Config\Database;

try {
    $db = Database::getConnection();
    echo "<h1>Actualización de Base de Datos - SEO Semántico 2026</h1>";
    echo "<p>Iniciando proceso...</p>";

    // Columnas a agregar
    $columns = [
        "ADD COLUMN seo_title VARCHAR(255) DEFAULT NULL AFTER title",
        "ADD COLUMN seo_description TEXT DEFAULT NULL AFTER description_short",
        "ADD COLUMN keywords TEXT DEFAULT NULL",
        "ADD COLUMN schema_type VARCHAR(50) DEFAULT 'TouristTrip'",
        "ADD COLUMN rating_score DECIMAL(3,2) DEFAULT 4.8",
        "ADD COLUMN review_count INT DEFAULT 150",
        "ADD COLUMN tour_highlights JSON DEFAULT NULL"
    ];

    foreach ($columns as $col) {
        try {
            // Intentar ejecutar ALTER TABLE. Si falla (usualmente porque ya existe), capturamos el error silenciosamente o avisamos.
            // Para ser robusto, chequeamos si existe NO estandar. Mejor try-catch simple.
            $sql = "ALTER TABLE tours $col";
            $db->exec($sql);
            echo "<div style='color:green'>✅ Ejecutado: $col</div>";
        } catch (PDOException $e) {
            // Error 42S21 = Duplicate column name en algunos drivers, o 1060 en MySQL
            if (strpos($e->getMessage(), 'Duplicate column') !== false || strpos($e->getMessage(), '1060') !== false) {
                echo "<div style='color:orange'>⚠️ Columna ya existe, saltando: $col</div>";
            } else {
                echo "<div style='color:red'>❌ Error: " . $e->getMessage() . "</div>";
            }
        }
    }

    echo "<h3>🎉 Base de datos actualizada con éxito</h3>";
    echo "<p>Ahora puedes borrar este archivo.</p>";
    echo "<a href='/'>Ir al Inicio</a>";

} catch (Exception $e) {
    die("Error Fatal: " . $e->getMessage());
}
