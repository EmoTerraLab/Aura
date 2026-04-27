-- =============================================================================
-- Aura — migrations.sql
-- Motor: SQLite
-- =============================================================================

PRAGMA foreign_keys = ON;
PRAGMA journal_mode = WAL;
PRAGMA synchronous = NORMAL;
PRAGMA busy_timeout = 5000;

-- Tabla `users`
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NULL,
    role VARCHAR(255) DEFAULT 'alumno' CHECK (role IN ('admin', 'direccion', 'orientador', 'profesor', 'alumno')),
    lang VARCHAR(5) DEFAULT NULL,
    totp_secret VARCHAR(64) DEFAULT NULL,
    totp_enabled INTEGER DEFAULT 0,
    totp_verified_at DATETIME DEFAULT NULL,
    webauthn_handle VARCHAR(64) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE UNIQUE INDEX IF NOT EXISTS idx_users_email ON users(email);

-- Tabla `classrooms`
CREATE TABLE IF NOT EXISTS classrooms (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    tutor_id INTEGER NULL,
    FOREIGN KEY (tutor_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Tabla `student_profiles`
CREATE TABLE IF NOT EXISTS student_profiles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER UNIQUE NOT NULL,
    classroom_id INTEGER NULL,
    anonymized_id VARCHAR(255) UNIQUE NOT NULL,
    allow_tracking BOOLEAN DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (classroom_id) REFERENCES classrooms(id) ON DELETE SET NULL
);
CREATE UNIQUE INDEX IF NOT EXISTS idx_student_user ON student_profiles(user_id);

-- Tabla `reports`
CREATE TABLE IF NOT EXISTS reports (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    student_id INTEGER NULL,
    classroom_id INTEGER NOT NULL,
    content TEXT NOT NULL,
    target VARCHAR(50) DEFAULT 'yo_mismo' CHECK (target IN ('compañero', 'profesor', 'otro', 'yo_mismo')),
    urgency_level VARCHAR(50) DEFAULT 'low' CHECK (urgency_level IN ('low', 'medium', 'high')),
    is_anonymous BOOLEAN DEFAULT 1,
    status VARCHAR(50) DEFAULT 'new' CHECK (status IN ('new', 'in_progress', 'resolved')),
    resolution_summary TEXT NULL,
    resolved_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES student_profiles(id) ON DELETE SET NULL,
    FOREIGN KEY (classroom_id) REFERENCES classrooms(id) ON DELETE CASCADE
);
CREATE INDEX IF NOT EXISTS idx_reports_classroom ON reports(classroom_id);
CREATE INDEX IF NOT EXISTS idx_reports_status ON reports(status);

-- Tabla `otp_codes`
CREATE TABLE IF NOT EXISTS otp_codes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    code VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    used BOOLEAN DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE INDEX IF NOT EXISTS idx_otp_user_code ON otp_codes(user_id, code);

-- Tabla `report_messages`
CREATE TABLE IF NOT EXISTS report_messages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    report_id INTEGER NOT NULL,
    sender_id INTEGER NOT NULL,
    message TEXT NOT NULL,
    is_internal BOOLEAN DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (report_id) REFERENCES reports(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE INDEX IF NOT EXISTS idx_messages_report ON report_messages(report_id);

-- Tabla `report_mentions`
CREATE TABLE IF NOT EXISTS report_mentions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    report_id INTEGER NOT NULL,
    message_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    is_read BOOLEAN DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (report_id) REFERENCES reports(id) ON DELETE CASCADE,
    FOREIGN KEY (message_id) REFERENCES report_messages(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE INDEX IF NOT EXISTS idx_mentions_user ON report_mentions(user_id);

-- Tabla `settings` [MEJORA i18n y Configuración]
CREATE TABLE IF NOT EXISTS settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    key VARCHAR(100) NOT NULL UNIQUE,
    value TEXT,
    type VARCHAR(20) DEFAULT 'text',
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Identidad de la escuela
INSERT OR IGNORE INTO settings (key, value, type) VALUES ('school_name', 'Mi Escuela', 'text');
INSERT OR IGNORE INTO settings (key, value, type) VALUES ('school_logo_url', '', 'text');
INSERT OR IGNORE INTO settings (key, value, type) VALUES ('school_contact_email', '', 'text');
INSERT OR IGNORE INTO settings (key, value, type) VALUES ('school_website', '', 'text');
INSERT OR IGNORE INTO settings (key, value, type) VALUES ('school_address', '', 'text');

-- Apariencia
INSERT OR IGNORE INTO settings (key, value, type) VALUES ('app_primary_color', '#004f56', 'color');
INSERT OR IGNORE INTO settings (key, value, type) VALUES ('app_accent_color', '#066972', 'color');
INSERT OR IGNORE INTO settings (key, value, type) VALUES ('footer_text', 'Aura powered by EmoTerraLab', 'text');

-- Idioma
INSERT OR IGNORE INTO settings (key, value, type) VALUES ('default_lang', 'es', 'text');

-- Correo (SMTP)
INSERT OR IGNORE INTO settings (key, value, type) VALUES ('mail_driver', 'smtp', 'text');
INSERT OR IGNORE INTO settings (key, value, type) VALUES ('mail_host', '', 'text');
INSERT OR IGNORE INTO settings (key, value, type) VALUES ('mail_port', '587', 'text');
INSERT OR IGNORE INTO settings (key, value, type) VALUES ('mail_encryption', 'tls', 'text');
INSERT OR IGNORE INTO settings (key, value, type) VALUES ('mail_username', '', 'text');
INSERT OR IGNORE INTO settings (key, value, type) VALUES ('mail_password', '', 'password');
INSERT OR IGNORE INTO settings (key, value, type) VALUES ('mail_from_address', '', 'text');
INSERT OR IGNORE INTO settings (key, value, type) VALUES ('mail_from_name', 'Aura', 'text');

-- Seguridad y 2FA
INSERT OR IGNORE INTO settings (key, value, type) VALUES ('2fa_students_method', 'webauthn', 'text');
INSERT OR IGNORE INTO settings (key, value, type) VALUES ('2fa_staff_method', 'totp', 'text');
INSERT OR IGNORE INTO settings (key, value, type) VALUES ('session_lifetime_minutes', '120', 'text');
INSERT OR IGNORE INTO settings (key, value, type) VALUES ('max_login_attempts', '5', 'text');

-- Tabla `totp_recovery_codes`
CREATE TABLE IF NOT EXISTS totp_recovery_codes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    code VARCHAR(255) NOT NULL,
    used INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabla `webauthn_credentials`
CREATE TABLE IF NOT EXISTS webauthn_credentials (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    credential_id TEXT NOT NULL UNIQUE,
    public_key TEXT NOT NULL,
    sign_count INTEGER DEFAULT 0,
    device_name VARCHAR(100) DEFAULT 'Mi dispositivo',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_used_at DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
