-- Guest checkout: allow unauthenticated purchase/lease requests, gated by an
-- emailed one-time verification code. Run after grand_transmission_auto_v2.sql.

ALTER TABLE orders
  MODIFY user_id INT(11) NULL,
  ADD COLUMN guest_name  VARCHAR(100) NULL AFTER user_id,
  ADD COLUMN guest_email VARCHAR(100) NULL AFTER guest_name,
  ADD COLUMN guest_phone VARCHAR(20)  NULL AFTER guest_email;

CREATE TABLE verification_codes (
  id         INT(11)      NOT NULL AUTO_INCREMENT,
  email      VARCHAR(100) NOT NULL,
  code_hash  VARCHAR(255) NOT NULL,
  purpose    VARCHAR(30)  NOT NULL DEFAULT 'guest_order',
  expires_at DATETIME     NOT NULL,
  used_at    DATETIME     NULL,
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_email_purpose (email, purpose)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
