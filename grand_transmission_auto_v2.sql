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

-- -------------------------------------------------------
-- CAR IMAGES: multiple images per car with ordering
-- -------------------------------------------------------
DROP TABLE IF EXISTS car_images;
CREATE TABLE car_images (
  id         INT(11)      NOT NULL AUTO_INCREMENT,
  car_id     INT(11)      NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  sort_order INT(11)      NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY fk_car_images_car (car_id),
  CONSTRAINT fk_car_images_car FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------
-- APPOINTMENTS: rebuilt with FK referencing cars(id)
-- -------------------------------------------------------
DROP TABLE IF EXISTS appointments;
CREATE TABLE appointments (
  appointment_id   INT(11)      NOT NULL AUTO_INCREMENT,
  car_id           INT(11)      NOT NULL,
  client_name      VARCHAR(100) DEFAULT NULL,
  client_email     VARCHAR(100) DEFAULT NULL,
  client_phone     VARCHAR(20)  DEFAULT NULL,
  appointment_date DATETIME     NOT NULL,
  status           ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
  employee_id      INT(11)      DEFAULT NULL,
  created_at       TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (appointment_id),
  KEY fk_appt_car (car_id),
  KEY fk_appt_emp (employee_id),
  CONSTRAINT fk_appt_car FOREIGN KEY (car_id)      REFERENCES cars(id)  ON DELETE CASCADE,
  CONSTRAINT fk_appt_emp FOREIGN KEY (employee_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------
-- SEED: Extra cars (Honda Civic 2003, Toyota Corolla 2002, Mitsubishi ASX 2020)
-- -------------------------------------------------------
INSERT INTO cars (brand, model, year, transmission, engine_spec, car_condition, description, image_path, color, price, lease_available, lease_terms, on_sale, discount, status) VALUES
(
  'Honda', 'Civic', 2003, 'Manual',
  '1.6L 4-cylinder 110hp',
  'Used',
  'A reliable and fuel-efficient compact that defined a generation. The 2003 Civic offers excellent build quality, low running costs, and easy maintenance — a perfect first car or daily driver.',
  'assets/images/Honda_civic_2003_front.jpeg',
  'Silver', 4500.00, 0, '', 0, 0.00, 'available'
),
(
  'Toyota', 'Corolla', 2002, 'Manual',
  '1.4L 4-cylinder 97hp',
  'Used',
  'One of the most reliable cars ever made. This 2002 Corolla has stood the test of time with minimal repairs, great fuel economy, and a comfortable ride for everyday use.',
  'assets/images/Toyota_corolla_2002_front.jpg',
  'Blue', 3800.00, 0, '', 0, 0.00, 'available'
),
(
  'Mitsubishi', 'ASX', 2020, 'Automatic',
  '2.0L 4-cylinder 150hp',
  'Used',
  'A stylish and practical compact SUV with modern safety features, spacious interior, and a smooth automatic gearbox. Great for city driving and weekend getaways.',
  'assets/images/Mitsubishi-ASX-2020-front.jpg',
  'Red', 18500.00, 1, '48 months, €320/month, 10% down', 0, 0.00, 'available'
);

INSERT INTO car_images (car_id, image_path, sort_order)
SELECT c.id, v.image_path, v.sort_order FROM cars c
JOIN (
  SELECT 'Honda'      AS brand, 'Civic'   AS model, 'assets/images/Honda_civic_2003_front.jpeg'     AS image_path, 0 AS sort_order UNION ALL
  SELECT 'Honda',               'Civic',              'assets/images/Honda_civic_2003_back.jpg',         1 UNION ALL
  SELECT 'Honda',               'Civic',              'assets/images/Hona_Civic_2003_interior.png',      2 UNION ALL
  SELECT 'Toyota',              'Corolla',             'assets/images/Toyota_corolla_2002_front.jpg',    0 UNION ALL
  SELECT 'Toyota',              'Corolla',             'assets/images/Toyota_corolla_2002_back.jpg',     1 UNION ALL
  SELECT 'Toyota',              'Corolla',             'assets/images/Toyota_corolla_2002_interior.jpg', 2 UNION ALL
  SELECT 'Mitsubishi',          'ASX',                 'assets/images/Mitsubishi-ASX-2020-front.jpg',    0 UNION ALL
  SELECT 'Mitsubishi',          'ASX',                 'assets/images/Mitsubishi-ASX-2020-back.jpg',     1 UNION ALL
  SELECT 'Mitsubishi',          'ASX',                 'assets/images/Mitsubishi-ASX-2020-interior.jpg', 2
) v ON c.brand = v.brand AND c.model = v.model AND c.year IN (2003, 2002, 2020);

SET FOREIGN_KEY_CHECKS = 1;
