<?php
// public/update_tours_2026.php
// Script de Migración DB - Nuevos Campos Tour
require_once __DIR__ . '/../src/Config/Database.php';

use App\Config\Database;

try {
    $db = Database::getConnection();
    echo "<h1>Iniciando Migración de Tours 2026...</h1>";

    // 1. Agregar columnas a tabla 'tours'
    $alterTours = "ALTER TABLE tours 
        ADD COLUMN IF NOT EXISTS info_cost TEXT NULL,
        ADD COLUMN IF NOT EXISTS info_dates_text TEXT NULL,
        ADD COLUMN IF NOT EXISTS info_duration TEXT NULL,
        ADD COLUMN IF NOT EXISTS info_includes TEXT NULL,
        ADD COLUMN IF NOT EXISTS info_visiting TEXT NULL,
        ADD COLUMN IF NOT EXISTS info_not_included TEXT NULL,
        ADD COLUMN IF NOT EXISTS info_departure TEXT NULL,
        ADD COLUMN IF NOT EXISTS info_parking TEXT NULL,
        ADD COLUMN IF NOT EXISTS info_important TEXT NULL,
        ADD COLUMN IF NOT EXISTS info_what_to_bring TEXT NULL,
        ADD COLUMN IF NOT EXISTS frequency_type ENUM('specific', 'daily', 'weekends', 'weekends_holidays') DEFAULT 'daily',
        ADD COLUMN IF NOT EXISTS specific_dates JSON NULL
    ;";

    $db->exec($alterTours);
    echo "<p>✅ Tabla 'tours' actualizada con nuevos campos de información.</p>";

    // 2. Agregar columna 'is_cover' a 'tour_images'
    // Primero verificamos si la tabla existe (por si acaso)
    $checkTable = $db->query("SHOW TABLES LIKE 'tour_images'");
    if ($checkTable->rowCount() > 0) {
        $alterImages = "ALTER TABLE tour_images 
            ADD COLUMN IF NOT EXISTS is_cover TINYINT(1) DEFAULT 0
        ;";
        $db->exec($alterImages);
        echo "<p>✅ Tabla 'tour_images' actualizada con flag de portada.</p>";
    } else {
        echo "<p>⚠️ Tabla 'tour_images' no encontrada. Se creará si es necesario en futuras migraciones.</p>";
    }

    echo "<h3>🚀 Migración Completada con Éxito.</h3>";
    echo "<p>Por favor, borra este archivo del servidor manualmente.</p>";

} catch (PDOException $e) {
    echo "<h2>❌ Error Fatal:</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
