-- WebDev 2 migration: add role column to users table
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS role ENUM('admin','employee','client') NOT NULL DEFAULT 'client';

-- Default admin account (password: password)
INSERT INTO users (name, email, password, role)
VALUES ('Admin', 'admin@gta.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE role = 'admin';

-- Default employee account (password: password)
INSERT INTO users (name, email, password, role)
VALUES ('Employee', 'employee@gta.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee')
ON DUPLICATE KEY UPDATE role = 'employee';

-- Default client account (password: password)
INSERT INTO users (name, email, password, role)
VALUES ('Client', 'client@gta.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client')
ON DUPLICATE KEY UPDATE role = 'client';
