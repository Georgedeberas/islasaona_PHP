<?php
// public/migrate_phase4.php
require __DIR__ . '/../src/Config/Database.php';

use App\Config\Database;

$db = Database::getConnection();

echo "<h1>🚀 Migrando Base de Datos - Fase 4 (Master Plan 2026)</h1>";
echo "<pre>";

try {
    // 1. Agregar columnas a la tabla 'tours'
    echo "Analizando tabla 'tours'...\n";

    // Lista de columnas a agregar
    $columns = [
        'deleted_at' => "ALTER TABLE tours ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL",
        'private_notes' => "ALTER TABLE tours ADD COLUMN private_notes TEXT NULL",
        'sort_order' => "ALTER TABLE tours ADD COLUMN sort_order INT DEFAULT 0"
    ];

    foreach ($columns as $col => $sql) {
        try {
            // Verificar si existe
            $check = $db->query("SHOW COLUMNS FROM tours LIKE '$col'");
            if ($check->rowCount() == 0) {
                $db->exec($sql);
                echo "✅ Columna '$col' agregada correctamente.\n";
            } else {
                echo "ℹ️ Columna '$col' ya existe. Saltando.\n";
            }
        } catch (PDOException $e) {
            echo "❌ Error agregando '$col': " . $e->getMessage() . "\n";
        }
    }

    // 2. Crear tabla 'redirects' (Fase 5 - Adelantando estructura)
    echo "\nVerificando tabla 'redirects' (Fase 5)...\n";
    $sqlRedirects = "CREATE TABLE IF NOT EXISTS redirects (
        id INT AUTO_INCREMENT PRIMARY KEY,
        old_slug VARCHAR(255) NOT NULL UNIQUE,
        new_url VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX(old_slug)
    )";
    $db->exec($sqlRedirects);
    echo "✅ Tabla 'redirects' verificada/creada.\n";

    // 3. Crear tabla 'activity_logs' (Fase 5 - Adelantando estructura)
    echo "\nVerificando tabla 'activity_logs' (Fase 5)...\n";
    $sqlLogs = "CREATE TABLE IF NOT EXISTS activity_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NULL,
        action VARCHAR(50) NOT NULL,  -- 'update', 'delete', 'create'
        entity_type VARCHAR(50) NOT NULL, -- 'tour', 'page'
        entity_id INT NULL,
        details TEXT NULL,
        ip_address VARCHAR(45) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $db->exec($sqlLogs);
    echo "✅ Tabla 'activity_logs' verificada/creada.\n";


    echo "\n✨ ¡Migración Completada con Éxito! puedes cerrar esta pestaña.";

} catch (Exception $e) {
    echo "\n💀 Error Critical: " . $e->getMessage();
}

echo "</pre>";
