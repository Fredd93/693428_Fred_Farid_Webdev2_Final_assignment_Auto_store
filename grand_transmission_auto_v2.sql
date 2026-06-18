-- WebDev 2 full migration
-- Run AFTER grand_transmission_auto.sql (the base schema)

SET FOREIGN_KEY_CHECKS = 0;

-- -------------------------------------------------------
-- USERS: rebuild to support name/email/password + roles
-- -------------------------------------------------------
DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id           INT(11)      NOT NULL AUTO_INCREMENT,
  name         VARCHAR(100) NOT NULL,
  email        VARCHAR(100) NOT NULL UNIQUE,
  password     VARCHAR(255) NOT NULL,
  role         ENUM('admin','employee','client') NOT NULL DEFAULT 'client',
  created_at   TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed accounts (password for all: password)
INSERT INTO users (name, email, password, role) VALUES
  ('Admin',    'admin@gta.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
  ('Employee', 'employee@gta.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee'),
  ('Client',   'client@gta.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client');

-- -------------------------------------------------------
-- CARS: rename PK to id, convert on_sale to TINYINT
-- -------------------------------------------------------
DROP TABLE IF EXISTS cars;
CREATE TABLE cars (
  id              INT(11)       NOT NULL AUTO_INCREMENT,
  brand           VARCHAR(50)   NOT NULL,
  model           VARCHAR(50)   NOT NULL,
  year            INT(11)       NOT NULL,
  transmission    VARCHAR(20)   DEFAULT NULL,
  engine_spec     VARCHAR(100)  DEFAULT NULL,
  car_condition   VARCHAR(20)   DEFAULT NULL,
  description     TEXT          DEFAULT NULL,
  image_path      VARCHAR(255)  DEFAULT NULL,
  color           VARCHAR(30)   DEFAULT NULL,
  price           DECIMAL(10,2) NOT NULL,
  lease_available TINYINT(1)    DEFAULT 0,
  lease_terms     TEXT          DEFAULT NULL,
  on_sale         TINYINT(1)    DEFAULT 0,
  discount        DECIMAL(5,2)  DEFAULT 0.00,
  status          ENUM('sold','available','reserved') DEFAULT 'available',
  created_at      TIMESTAMP     NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------
-- ORDERS: rebuild with user_id FK + notes
-- -------------------------------------------------------
DROP TABLE IF EXISTS orders;
CREATE TABLE orders (
  id          INT(11)  NOT NULL AUTO_INCREMENT,
  user_id     INT(11)  NOT NULL,
  car_id      INT(11)  NOT NULL,
  order_type  ENUM('purchase','lease') NOT NULL,
  status      ENUM('pending','approved','denied','completed') DEFAULT 'pending',
  notes       TEXT     DEFAULT NULL,
  created_at  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY fk_orders_user (user_id),
  KEY fk_orders_car  (car_id),
  CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
  CONSTRAINT fk_orders_car  FOREIGN KEY (car_id)  REFERENCES cars  (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
