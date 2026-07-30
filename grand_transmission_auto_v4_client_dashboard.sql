-- Client dashboard "My Cars" + lease installments + contact info visibility.
-- Run after grand_transmission_auto_v3_guest_checkout.sql.

ALTER TABLE users
  ADD COLUMN phone   VARCHAR(20)  NULL AFTER email,
  ADD COLUMN address VARCHAR(255) NULL AFTER phone;

ALTER TABLE appointments
  ADD COLUMN purpose VARCHAR(20) NOT NULL DEFAULT 'test_drive' AFTER car_id;

ALTER TABLE orders
  ADD COLUMN completed_at DATETIME NULL AFTER updated_at;

CREATE TABLE lease_installments (
  id             INT(11)       NOT NULL AUTO_INCREMENT,
  order_id       INT(11)       NOT NULL,
  installment_no INT(11)       NOT NULL,
  due_date       DATE          NOT NULL,
  amount         DECIMAL(10,2) NOT NULL,
  status         ENUM('due','paid') NOT NULL DEFAULT 'due',
  paid_at        DATETIME      NULL,
  created_at     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_order (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
