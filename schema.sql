CREATE TABLE IF NOT EXISTS tributes (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(120) NOT NULL,
    relationship VARCHAR(40) NULL,
    message    TEXT NULL,
    status     ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tributes_status_created (status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS media (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tribute_id    INT UNSIGNED NULL,
    type          ENUM('photo','video_file','youtube') NOT NULL,
    file_path     VARCHAR(255) NULL,
    youtube_id    VARCHAR(20)  NULL,
    caption       VARCHAR(255) NULL,
    uploader_name VARCHAR(120) NULL,
    status        ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_media_tribute FOREIGN KEY (tribute_id) REFERENCES tributes(id) ON DELETE SET NULL,
    INDEX idx_media_status_created (status, created_at),
    INDEX idx_media_tribute (tribute_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
