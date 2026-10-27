-- Adds what the customer flow needs: checkout with variants and QR PH
-- reference numbers, contact messages, password resets and login lockout.
-- Run once on a database created from an older schema.sql:
--   mysql -u root -padmin123 < database/migrations/001_customer_flow.sql

USE daily_spot_cafe;

-- user_details.user_id now mirrors users.user_id instead of auto-incrementing
ALTER TABLE user_details MODIFY user_id INT NOT NULL;
ALTER TABLE user_details
    ADD CONSTRAINT fk_user_details_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE;

ALTER TABLE orders
    ADD COLUMN reference_number VARCHAR(50) NULL AFTER payment_type;

ALTER TABLE order_items
    ADD COLUMN variant_type VARCHAR(50) NULL AFTER product_id,
    ADD COLUMN size_label   VARCHAR(50) NULL AFTER variant_type;

CREATE TABLE IF NOT EXISTS messages (
    message_id  INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NULL,
    name        VARCHAR(150) NOT NULL,
    email       VARCHAR(255) NOT NULL,
    subject     VARCHAR(200) NOT NULL,
    message     TEXT NOT NULL,
    is_read     TINYINT(1) NOT NULL DEFAULT 0,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (user_id)
);

CREATE TABLE IF NOT EXISTS password_resets (
    reset_id    INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    token_hash  CHAR(64) NOT NULL UNIQUE,
    expires_at  DATETIME NOT NULL,
    used_at     DATETIME NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS login_attempts (
    username      VARCHAR(100) PRIMARY KEY,
    failed_count  INT NOT NULL DEFAULT 0,
    locked_until  DATETIME NULL,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
