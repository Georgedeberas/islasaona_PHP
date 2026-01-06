<?php
// public/update_db.php
// Script de reparación de DB y Diagnóstico

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../src/autoload.php';

use App\Config\Database;

echo "<h1>Diagnóstico y Actualización de DB</h1>";

try {
    $db = Database::getConnection();
    echo "<p style='color:green'>✅ Conexión a Base de Datos EXITOSA.</p>";

    // Tablas
    $sql = "
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

    // Ejecutar creación de tablas
    $db->exec($sql);
    echo "<p style='color:green'>✅ Tablas verificadas/creadas.</p>";

    // Crear Admin por defecto si no existe
    $email = 'admin@mochilerosrd.com';
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() == 0) {
        $passHash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (email, password_hash, role) VALUES (?, ?, 'admin')");
        $stmt->execute([$email, $passHash]);
        echo "<p style='color:green'>✅ Usuario Admin creado: $email / admin123</p>";
    } else {
        echo "<p style='color:blue'>ℹ️ El usuario admin ya existe.</p>";
    }

    // Check de permisos carpeta uploads
    $uploadPath = __DIR__ . '/assets/uploads';
    if (!is_dir($uploadPath)) {
        if (mkdir($uploadPath, 0755, true)) {
            echo "<p style='color:green'>✅ Carpeta uploads creada.</p>";
        } else {
            echo "<p style='color:red'>❌ Error creando carpeta uploads. Hazlo manualmente.</p>";
        }
    }

    if (is_writable($uploadPath)) {
        echo "<p style='color:green'>✅ Carpeta uploads tiene permisos de escritura.</p>";
    } else {
        echo "<p style='color:red; font-weight:bold;'>❌ Carpeta uploads NO es escribible. Necesita permisos 755 o 777.</p>";
    }

    echo "<hr><h3>Todo listo. Elimina este archivo 'update_db.php' y prueba el sitio.</h3>";
    echo "<a href='/'>Ir al Inicio</a> | <a href='/admin/login'>Ir al Admin</a>";

} catch (Exception $e) {
    echo "<h2 style='color:red'>Error Fatal:</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
