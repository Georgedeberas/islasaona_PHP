<?php
// public/migrate_cms_expansion.php
require __DIR__ . '/../src/Config/Database.php';
use App\Config\Database;

$db = Database::getConnection();

echo "<h1>🚀 Migración CMS Expansión 2026</h1><pre>";

try {
    // 1. TABLA ARTICLES (BLOG)
    echo "Creating 'articles' table...\n";
    $sqlArticles = "CREATE TABLE IF NOT EXISTS articles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL UNIQUE,
        image_path VARCHAR(255),
        content LONGTEXT,
        excerpt TEXT,
        author_id INT,
        is_published BOOLEAN DEFAULT TRUE,
        views INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        seo_title VARCHAR(255),
        seo_description TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $db->exec($sqlArticles);
    echo "✅ Tabla 'articles' lista.\n";

    // 2. ACTUALIZAR USERS (ROLES + CAMPOS EXTRA)
    echo "Updating 'users' table...\n";
    // Check if columns exist to avoid errors
    $cols = $db->query("DESCRIBE users")->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('name', $cols)) {
        $db->exec("ALTER TABLE users ADD COLUMN name VARCHAR(100) DEFAULT 'Usuario'");
        echo "✅ Columna 'name' añadida.\n";
    }
    if (!in_array('avatar', $cols)) {
        $db->exec("ALTER TABLE users ADD COLUMN avatar VARCHAR(255) DEFAULT NULL");
        echo "✅ Columna 'avatar' añadida.\n";
    }
    // Ensure role exists (it was in schema but let's be safe)
    if (!in_array('role', $cols)) {
        $db->exec("ALTER TABLE users ADD COLUMN role VARCHAR(50) DEFAULT 'admin'");
        echo "✅ Columna 'role' añadida.\n";
    }

    // 3. NUEVOS SETTINGS GLOBALES
    echo "Inserting new global settings...\n";
    $newSettings = [
        'theme_primary_color' => '#FFC107', // Warning/Yellow default
        'whatsapp_number' => '18290000000',
        'home_hero_height' => '85', // Default 85vh
    ];

    $check = $db->prepare("SELECT id FROM settings WHERE setting_key = ?");
    $insert = $db->prepare("INSERT INTO settings (setting_key, setting_value, label, type) VALUES (?, ?, ?, ?)");

    foreach ($newSettings as $key => $val) {
        $check->execute([$key]);
        if ($check->rowCount() == 0) {
            $label = ucwords(str_replace('_', ' ', $key));
            $insert->execute([$key, $val, $label, 'text']);
            echo "✅ Setting '$key' creado.\n";
        }
    }

    echo "\n✨ MIGRACIÓN EXITOSA.";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
echo "</pre>";
