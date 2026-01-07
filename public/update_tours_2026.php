<?php
// public/update_tours_2026.php
// Script de Migración DB - Nuevos Campos Tour
// Compatibilidad MySQL 5.7 / MariaDB 10.x Antigua

require_once __DIR__ . '/../src/Config/Database.php';

use App\Config\Database;

// Función Helper para chequear columna
function columnExists($db, $table, $column)
{
    try {
        $stmt = $db->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}

try {
    $db = Database::getConnection();
    echo "<h1>Iniciando Migración de Tours 2026 (Version Compatible)...</h1>";

    // 1. Agregar columnas a 'tours'
    // Como versiones viejas de MySQL no soportan "ADD COLUMN IF NOT EXISTS", verificamos con PHP

    $columns = [
        ['info_cost', 'TEXT NULL'],
        ['info_dates_text', 'TEXT NULL'],
        ['info_duration', 'TEXT NULL'],
        ['info_includes', 'TEXT NULL'],
        ['info_visiting', 'TEXT NULL'],
        ['info_not_included', 'TEXT NULL'],
        ['info_departure', 'TEXT NULL'],
        ['info_parking', 'TEXT NULL'],
        ['info_important', 'TEXT NULL'],
        ['info_what_to_bring', 'TEXT NULL'],
        ['frequency_type', "ENUM('specific', 'daily', 'weekends', 'weekends_holidays') DEFAULT 'daily'"],
        ['specific_dates', 'JSON NULL']
    ];

    foreach ($columns as $col) {
        $name = $col[0];
        $def = $col[1];

        if (!columnExists($db, 'tours', $name)) {
            $sql = "ALTER TABLE tours ADD COLUMN $name $def";
            $db->exec($sql);
            echo "<p>✅ Columna <b>$name</b> creada.</p>";
        } else {
            echo "<p>ℹ️ Columna <b>$name</b> ya existe. Saltada.</p>";
        }
    }

    // 2. Agregar 'is_cover' a 'tour_images'
    $checkTable = $db->query("SHOW TABLES LIKE 'tour_images'");
    if ($checkTable->rowCount() > 0) {
        if (!columnExists($db, 'tour_images', 'is_cover')) {
            $db->exec("ALTER TABLE tour_images ADD COLUMN is_cover TINYINT(1) DEFAULT 0");
            echo "<p>✅ Columna <b>is_cover</b> creada en tour_images.</p>";
        } else {
            echo "<p>ℹ️ Columna <b>is_cover</b> ya existe.</p>";
        }
    } else {
        echo "<p>⚠️ Tabla 'tour_images' no encontrada.</p>";
    }

    echo "<h3>🚀 Migración Completada.</h3>";
    echo "<p>Si ves esto, la Base de Datos ya está actualizada.</p>";

} catch (PDOException $e) {
    echo "<h2>❌ Error SQL:</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
