<?php
// public/update_pages_order.php
// Script de Migración DB - Ordenamiento de Páginas

require_once __DIR__ . '/../src/Config/Database.php';

use App\Config\Database;

try {
    $db = Database::getConnection();
    echo "<h1>Agregando Columna de Orden a Páginas...</h1>";

    // Verificar si la columna ya existe
    $stmt = $db->query("SHOW COLUMNS FROM `pages` LIKE 'order_index'");
    if ($stmt->rowCount() == 0) {
        // No existe, agregarla
        // ADD COLUMN IF NOT EXISTS no soportado en MySQL viejo, pero ya verificamos con PHP
        $sql = "ALTER TABLE pages ADD COLUMN order_index INT DEFAULT 0 AFTER slug";
        $db->exec($sql);
        echo "<p>✅ Columna <b>order_index</b> agregada correctamente.</p>";
    } else {
        echo "<p>ℹ️ La columna <b>order_index</b> ya existía.</p>";
    }

    echo "<h3>🚀 Migración Completada.</h3>";
    echo "<p>Por favor, borra este archivo del servidor manualmente.</p>";

} catch (PDOException $e) {
    echo "<h2>❌ Error Fatal:</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
