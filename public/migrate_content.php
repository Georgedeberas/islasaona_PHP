<?php
// public/migrate_content.php
require __DIR__ . '/../src/Config/Database.php';
use App\Config\Database;

$db = Database::getConnection();

echo "<h1>🎨 Migración Contenido Home & Footer</h1>";
echo "<pre>";

// Lista de ajustes predeterminados para NO romper el diseño
$settings = [
    // 1. HERO SECTION
    'home_hero_title' => 'Tours Saona VIP',
    'home_hero_subtitle' => 'La experiencia más completa en Isla Saona',
    'home_hero_bg' => 'assets/img/hero_saona.webp', // Asumiendo ruta
    'home_hero_cta_text' => 'Ver Ofertas',
    'home_hero_cta_link' => '#tours',

    // 2. SECCIONES (Toggles)
    'home_show_why' => '1',
    'home_show_welcome' => '1',

    // 3. FEATURED TOURS (Array JSON)
    'home_featured_tours' => json_encode(['1', '2', '3']), // Default IDs

    // 4. FOOTER & CONTACT
    'contact_phone' => '18290000000',
    'contact_email' => 'info@mochilerosrd.com',
    'contact_address' => 'Bayahibe, República Dominicana',
    'contact_hours' => 'Lunes a Domingo: 8:00 AM - 10:00 PM',

    // 5. SOCIAL MEDIA
    'social_facebook' => 'https://facebook.com/mochilerosrd',
    'social_instagram' => 'https://instagram.com/mochilerosrd',
    'social_tiktok' => '',
    'social_tripadvisor' => '',

    // 6. LEGAL
    'legal_copyright' => 'Mochileros RD',
    'legal_privacy_link' => '#',
    'legal_terms_link' => '#'
];

try {
    $stmt = $db->prepare("SELECT id FROM settings WHERE setting_key = ?");
    $insert = $db->prepare("INSERT INTO settings (setting_key, setting_value, label, type) VALUES (?, ?, ?, ?)");

    foreach ($settings as $key => $val) {
        $stmt->execute([$key]);
        if ($stmt->rowCount() == 0) {
            $label = ucfirst(str_replace('_', ' ', $key));
            $type = 'text';
            if (strpos($key, 'show') !== false)
                $type = 'boolean';
            if (strpos($key, 'featured') !== false)
                $type = 'json';

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
