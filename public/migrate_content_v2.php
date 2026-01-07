<?php
// public/migrate_content_v2.php
require __DIR__ . '/../src/Config/Database.php';
use App\Config\Database;

$db = Database::getConnection();

echo "<h1>🎨 Migración Content V2 (Missing Sections)</h1>";
echo "<pre>";

$settings = [
    'home_welcome_title' => 'Bienvenido a Isla Saona',
    'home_welcome_text' => 'Descubre el paraíso número 1 de República Dominicana con Mochileros RD. Experiencias únicas, seguridad garantizada y el mejor ambiente para ti y tu familia.',
    'home_why_title' => '¿Por qué viajar con nosotros?',
    'home_why_text' => 'Somos expertos locales con más de 10 años de experiencia. Sin intermediarios, precios justos y atención personalizada 24/7.'
];

try {
    $stmt = $db->prepare("SELECT id FROM settings WHERE setting_key = ?");
    $insert = $db->prepare("INSERT INTO settings (setting_key, setting_value, label, type) VALUES (?, ?, ?, ?)");

    foreach ($settings as $key => $val) {
        $stmt->execute([$key]);
        if ($stmt->rowCount() == 0) {
            $label = ucfirst(str_replace('_', ' ', $key));
            $type = 'textarea';
            if (strpos($key, 'title') !== false)
                $type = 'text';

            $insert->execute([$key, $val, $label, $type]);
            echo "✅ Creado: $key\n";
        } else {
            echo "ℹ️ Ya existe: $key\n";
        }
    }
    echo "\n✨ Todo listo.";
} catch (Exception $e) {
    echo "💀 Error: " . $e->getMessage();
}
echo "</pre>";
