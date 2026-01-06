-- Tabla de Usuarios (Administradores)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Tours
CREATE TABLE IF NOT EXISTS tours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description_short TEXT,
    description_long MEDIUMTEXT, -- HTML permitido
    price_adult DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    price_child DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    duration VARCHAR(100), -- Ej: "8 Horas", "Full Day"
    includes JSON, -- Lista de qué incluye
    not_included JSON, -- Lista de qué no incluye
    is_active BOOLEAN DEFAULT TRUE,
    display_style ENUM('grid', 'list', 'featured') DEFAULT 'grid',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    meta_title VARCHAR(255),
    meta_description TEXT,
    INDEX idx_slug (slug),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Imágenes de Tours
CREATE TABLE IF NOT EXISTS tour_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tour_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    is_cover BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar usuario admin por defecto (Password: admin123)
-- El hash debe generarse con password_hash('admin123', PASSWORD_DEFAULT)
-- Usaremos un placeholder, el usuario deberá actualizarlo o usar el script de seed
INSERT INTO users (email, password_hash, role) VALUES 
('admin@mochilerosrd.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE id=id;
