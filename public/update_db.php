<?php
// public/update_db.php
// Script de reparación de DB y Diagnóstico V2 (CMS Update)

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../src/autoload.php';

use App\Config\Database;

echo "<h1>Diagnóstico y Actualización de DB (Fase CMS)</h1>";

try {
    $db = Database::getConnection();
    echo "<p style='color:green'>✅ Conexión a Base de Datos EXITOSA.</p>";

    // 1. Tablas Core (Ya deberían existir, usamos IF NOT EXISTS)
    $sqlCore = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        role VARCHAR(50) DEFAULT 'admin',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS tours (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL UNIQUE,
        description_short TEXT,
        description_long MEDIUMTEXT,
        price_adult DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
        price_child DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
        duration VARCHAR(100),
        includes JSON,
        not_included JSON,
        is_active BOOLEAN DEFAULT TRUE,
        display_style ENUM('grid', 'list', 'featured') DEFAULT 'grid',
        meta_title VARCHAR(255),
        meta_description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_slug (slug)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS tour_images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tour_id INT NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        is_cover BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    $db->exec($sqlCore);
    echo "<p style='color:green'>✅ Tablas Core verificadas.</p>";

    // 2. Tablas CMS (Settings & Pages)
    $sqlCMS = "
    CREATE TABLE IF NOT EXISTS settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(50) NOT NULL UNIQUE,
        setting_value TEXT,
        label VARCHAR(100),
        type ENUM('text', 'textarea', 'image') DEFAULT 'text',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS pages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        slug VARCHAR(50) NOT NULL UNIQUE,
        title VARCHAR(255) NOT NULL,
        content LONGTEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    $db->exec($sqlCMS);
    echo "<p style='color:green'>✅ Tablas CMS verificadas/creadas.</p>";

    // 3. Seed Default Settings
    $defaultSettings = [
        ['company_name', 'Mochileros RD', 'Nombre de la Empresa'],
        ['phone_main', '1-829-000-0000', 'Teléfono Principal'],
        ['whatsapp_number', '18290000000', 'Número WhatsApp (Sin guiones)'],
        ['email_contact', 'info@mochilerosrd.com', 'Email de Contacto'],
        ['address', 'Santo Domingo, República Dominicana', 'Dirección Física'],
        ['facebook_url', '#', 'URL Facebook'],
        ['instagram_url', '#', 'URL Instagram'],
        ['tiktok_url', '#', 'URL TikTok'],
        ['footer_text', 'Explora los rincones más bellos de República Dominicana con nosotros.', 'Texto Footer']
    ];

    $stmtSet = $db->prepare("INSERT IGNORE INTO settings (setting_key, setting_value, label) VALUES (?, ?, ?)");
    foreach ($defaultSettings as $setting) {
        $stmtSet->execute($setting);
    }
    echo "<p style='color:green'>✅ Configuración base insertada.</p>";

    // 4. Seed Default Pages
    $defaultPages = [
        ['about', 'Sobre Nosotros', '<h1>¡Hola! Somos Mochileros RD</h1><p>Tu agencia de confianza para conocer República Dominicana.</p>'],
        ['contact', 'Contacto', '<p>Ponte en contacto con nosotros.</p>']
    ];
    $stmtPage = $db->prepare("INSERT IGNORE INTO pages (slug, title, content) VALUES (?, ?, ?)");
    foreach ($defaultPages as $page) {
        $stmtPage->execute($page);
    }
    echo "<p style='color:green'>✅ Páginas estáticas base insertadas.</p>";

    // Check de permisos carpeta uploads
    $uploadPath = __DIR__ . '/assets/uploads';
    if (!is_dir($uploadPath)) {
        if (mkdir($uploadPath, 0755, true)) {
            echo "<p style='color:green'>✅ Carpeta uploads creada.</p>";
        } else {
            echo "<p style='color:red'>❌ Error creando carpeta uploads.</p>";
        }
    }

    // Check carpeta images (Logos)
    $imagesPath = __DIR__ . '/assets/images';
    if (!is_dir($imagesPath)) {
        if (mkdir($imagesPath, 0755, true)) {
            echo "<p style='color:green'>✅ Carpeta assets/images creada.</p>";
        }
    }

    // 5. Tabla Analytics (Propia)
    $sqlAnalytics = "
    CREATE TABLE IF NOT EXISTS analytics_visits (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ip_address VARCHAR(45),
        visitor_id VARCHAR(50),
        page_url VARCHAR(255),
        referrer TEXT,
        user_agent TEXT,
        country_code VARCHAR(3) DEFAULT 'XX',
        city VARCHAR(100) DEFAULT 'Unknown',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_created_at (created_at),
        INDEX idx_visitor (visitor_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    $db->exec($sqlAnalytics);
    echo "<p style='color:green'>✅ Tabla de Analíticas activada.</p>";

    echo "<hr><h3>Actualización Completa.</h3>";
    echo "<a href='/'>Ir al Inicio</a> | <a href='/admin/login'>Ir al Admin</a>";

} catch (Exception $e) {
    echo "<h2 style='color:red'>Error Fatal:</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
