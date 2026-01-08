<?php
// public/migrate_cms_complete.php
require __DIR__ . '/../src/Config/Database.php';
use App\Config\Database;

$db = Database::getConnection();

echo "<h1>🚀 Migración Completa CMS (Home & Footer)</h1><pre>";

// 1. LISTA COMPLETA DE SETTINGS
$settings = [
    // --- HERO SECTION ---
    'home_hero_title' => 'Tours Saona VIP',
    'home_hero_subtitle' => 'La experiencia más completa en Isla Saona',
    'home_hero_bg' => 'assets/img/hero_saona.webp',
    'home_hero_cta_text' => 'Ver Ofertas',
    'home_hero_cta_link' => '#tours',

    // --- SECCIONES DYNAMIC ---
    'home_show_welcome' => '1',
    'home_welcome_title' => 'Bienvenidos a Mochileros RD',
    'home_welcome_text' => 'Somos una agencia de viajes dedicada a ofrecer las mejores experiencias...',

    'home_show_why' => '1',
    'home_why_title' => '¿Por qué viajar con nosotros?',
    'home_why_text' => 'Seguridad, Confianza y Calidad garantizada.',

    // --- FEATURED TOURS (JSON) ---
    'home_featured_tours' => json_encode(['1', '2', '3']),

    // --- FOOTER & CONTACT ---
    'contact_phone' => '1-809-555-5555',
    'contact_email' => 'info@mochilerosrd.com',
    'contact_address' => 'Bayahibe, La Altagracia, RD',
    'contact_hours' => 'Lunes a Domingo: 8:00 AM - 10:00 PM',

    // --- SOCIAL ---
    'social_facebook' => 'https://facebook.com/',
    'social_instagram' => 'https://instagram.com/',
    'social_tiktok' => '',
    'social_tripadvisor' => '',

    // --- LEGAL ---
    'legal_copyright' => 'Mochileros RD',
    'legal_privacy_link' => '#',
    'legal_terms_link' => '#'
];

try {
    $check = $db->prepare("SELECT id FROM settings WHERE setting_key = ?");
    $insert = $db->prepare("INSERT INTO settings (setting_key, setting_value, label, type) VALUES (?, ?, ?, ?)");

    foreach ($settings as $key => $val) {
        $check->execute([$key]);
        if ($check->rowCount() == 0) {
            // Determinar Tipo y Etiqueta
            $label = ucwords(str_replace('_', ' ', $key));
            $type = 'text';

            if (strpos($key, 'show') !== false)
                $type = 'boolean'; // Switch
            if (strpos($key, 'text') !== false || strpos($key, 'subtitle') !== false)
                $type = 'textarea';
            if (strpos($key, 'featured') !== false)
                $type = 'json';

            $insert->execute([$key, $val, $label, $type]);
            echo "✅ Insertado: $key\n";
        } else {
            echo "ℹ️ Ya existe (omitido): $key\n";
        }
    }
    echo "\n✨ MIGRACIÓN COMPLETADA EXITOSAMENTE.";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
echo "</pre>";
