<?php
// public/migrate_tracking.php
require __DIR__ . '/../src/Config/Database.php';
use App\Config\Database;

$db = Database::getConnection();

echo "<h1>📊 Migración Tracking de Ventas</h1>";
echo "<pre>";

try {
    $sql = "CREATE TABLE IF NOT EXISTS click_tracks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        track_type VARCHAR(50) NOT NULL DEFAULT 'whatsapp',
        entity_type VARCHAR(50) NULL,
        entity_id INT NULL,
        source VARCHAR(50) NULL,
        ip_address VARCHAR(45) NULL,
        user_agent TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX(track_type),
        INDEX(created_at)
    )";
    $db->exec($sql);
    echo "✅ Tabla 'click_tracks' creada correctamente.\n";

} catch (Exception $e) {
    echo "💀 Error: " . $e->getMessage();
}
echo "</pre>";
