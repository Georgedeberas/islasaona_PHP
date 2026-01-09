<?php
// public/update_db.php
// Script temporal para actualizar la base de datos (Fase 7)

require_once __DIR__ . '/../src/autoload.php';

use App\Config\Database;

try {
    $db = Database::getConnection();

    echo "<h1>Actualizando Base de Datos...</h1>";

    $sql = "
    CREATE TABLE IF NOT EXISTS click_tracks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        track_type VARCHAR(50) NOT NULL,
        entity_type VARCHAR(50) NULL,
        entity_id INT NULL,
        source VARCHAR(50) NULL,
        ip_address VARCHAR(45) NULL,
        user_agent TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";

    $db->exec($sql);

    echo "<p style='color:green'>✅ Tabla 'click_tracks' creada o verificada correctamente.</p>";
    echo "<p>La actualización ha finalizado con éxito.</p>";
    echo "<p><strong>Por seguridad, elimina este archivo del servidor o repositorio cuando confirmes que funciona.</strong></p>";
    echo "<a href='/admin/dashboard'>Volver al Dashboard</a>";

} catch (Exception $e) {
    echo "<h1 style='color:red'>Error Crítico</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
