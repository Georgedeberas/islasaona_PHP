<?php
// public/migrate_phase5.php
require __DIR__ . '/../src/Config/Database.php';
use App\Config\Database;

$db = Database::getConnection();

echo "<h1>🛠️ Migración Fase 5: Control y Sistema</h1>";
echo "<pre>";

try {
    // 1. Maintenance Mode
    $sql = "SELECT id FROM settings WHERE setting_key = 'maintenance_mode'";
    if ($db->query($sql)->rowCount() == 0) {
        echo "Creando ajuste 'maintenance_mode'...\n";
        // Type 'boolean' or 'switch' isn't standard in our simple system, using 'text' or 'number' (0/1)
        // Let's use 'number' for simplicity 0/1
        $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value, label, type) VALUES (?, ?, ?, ?)");
        $stmt->execute(['maintenance_mode', '0', 'Modo Mantenimiento (1=On, 0=Off)', 'number']);
        echo "✅ Ajuste creado.\n";
    } else {
        echo "ℹ️ Ajuste 'maintenance_mode' ya existe.\n";
    }

    echo "\n✨ Migración Fase 5 completada.";

} catch (Exception $e) {
    echo "\n💀 Error: " . $e->getMessage();
}
echo "</pre>";
