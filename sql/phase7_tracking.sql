CREATE TABLE IF NOT EXISTS click_tracks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    track_type VARCHAR(50) NOT NULL, -- whatsapp, call, etc
    entity_type VARCHAR(50) NULL, -- tour, general
    entity_id INT NULL,
    source VARCHAR(50) NULL, -- header, footer, detail_btn
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
