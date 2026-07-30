-- Track which employee/admin closed a sale.
-- Run after grand_transmission_auto_v4_client_dashboard.sql.

ALTER TABLE orders ADD COLUMN completed_by INT(11) NULL AFTER completed_at;
