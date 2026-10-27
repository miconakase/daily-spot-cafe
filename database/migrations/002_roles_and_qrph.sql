-- Matches the original design (DSC schema): a roles table, and QR PH stored
-- as 'QRPH'. Run once after 001_customer_flow.sql:
--   mysql -u root -padmin123 < database/migrations/002_roles_and_qrph.sql

USE daily_spot_cafe;

CREATE TABLE IF NOT EXISTS roles (
    role_id    INT PRIMARY KEY,
    role_name  VARCHAR(45) NOT NULL
);
INSERT IGNORE INTO roles (role_id, role_name) VALUES (1, 'Admin'), (2, 'Customer');

ALTER TABLE users
    ADD CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(role_id);

UPDATE orders SET payment_type = 'QRPH' WHERE payment_type = 'QR PH';
