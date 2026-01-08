<?php
// public/migrate_hero_v2.php
require __DIR__ . '/../src/Config/Database.php';
use App\Config\Database;

$db = Database::getConnection();

echo "<h1>🚀 Migración Hero V2 (Portada Premium)</h1><pre>";

$settings = [
    'home_hero_supra_title' => 'Descubre el paraíso',
    'home_hero_title_2' => 'ISLA SAONA',
    'home_hero_bg_width' => '100', // Default 100%
];

try {
    $check = $db->prepare("SELECT id FROM settings WHERE setting_key = ?");
    $insert = $db->prepare("INSERT INTO settings (setting_key, setting_value, label, type) VALUES (?, ?, ?, ?)");

    foreach ($settings as $key => $val) {
        $check->execute([$key]);
        if ($check->rowCount() == 0) {
            $label = ucwords(str_replace('_', ' ', $key));
            $type = 'text';
            if ($key === 'home_hero_bg_width')
                $type = 'number';

            $insert->execute([$key, $val, $label, $type]);
            echo "✅ Insertado: $key\n";
        } else {
            echo "ℹ️ Ya existe (omitido): $key\n";
        }
    }
    echo "\n✨ MIGRACIÓN V2 COMPLETADA.";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
echo "</pre>";
