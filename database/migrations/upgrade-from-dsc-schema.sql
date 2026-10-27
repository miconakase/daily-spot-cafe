-- ============================================================================
-- Daily Spot Cafe: upgrade an OLD database (the original DSC_Schema.sql
-- structure) to the current structure, keeping all existing data.
--
--   * Run ONCE, on a database that has NOT been upgraded yet.
--   * BACK UP FIRST (MySQL Workbench: Server > Data Export).
--   * In Workbench: File > Open SQL Script..., then Execute (lightning bolt).
--
-- Databases created from database/schema.sql do not need this script.
-- ============================================================================

USE daily_spot_cafe;

SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------- roles ----
INSERT IGNORE INTO roles (role_id, role_name) VALUES (1, 'Admin'), (2, 'Customer');

-- ---------------------------------------------------------------- users ----
-- password: 45 chars was too short for hashed passwords (60+). Old plain-text
-- passwords keep working and are converted to hashes on each user's next login.
ALTER TABLE users
    MODIFY user_id   INT NOT NULL AUTO_INCREMENT,
    MODIFY username  VARCHAR(100) NOT NULL,
    MODIFY password  VARCHAR(255) NOT NULL,
    MODIFY role_id   INT NOT NULL DEFAULT 2,
    ADD UNIQUE KEY uq_users_username (username),
    ADD CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(role_id);

-- --------------------------------------------------------- user_details ----
-- user_id now mirrors users.user_id instead of auto-incrementing on its own.
ALTER TABLE user_details
    MODIFY user_id       INT NOT NULL,
    MODIFY first_name    VARCHAR(100) NOT NULL DEFAULT '',
    MODIFY middle_name   VARCHAR(100) NULL DEFAULT '',
    MODIFY last_name     VARCHAR(100) NOT NULL DEFAULT '',
    MODIFY suffix        VARCHAR(20)  NULL DEFAULT '',
    MODIFY phone_number  VARCHAR(30)  NOT NULL DEFAULT '',
    MODIFY email         VARCHAR(255) NOT NULL DEFAULT '',
    MODIFY updated_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ADD CONSTRAINT fk_user_details_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE;

-- ----------------------------------------------------------- categories ----
ALTER TABLE categories
    MODIFY category_name  VARCHAR(100) NOT NULL,
    MODIFY description    TEXT NULL,
    MODIFY is_active      TINYINT(1) NOT NULL DEFAULT 1,
    MODIFY has_variants   TINYINT(1) NOT NULL DEFAULT 0,
    MODIFY has_sizes      TINYINT(1) NOT NULL DEFAULT 0;

-- ------------------------------------------------------------- products ----
ALTER TABLE products
    ADD COLUMN price DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER product_name,
    MODIFY product_name       VARCHAR(150) NOT NULL,
    MODIFY short_description  VARCHAR(255) NOT NULL DEFAULT '',
    MODIFY long_description   TEXT NULL,
    MODIFY category_id        INT NULL,
    MODIFY is_published       TINYINT(1) NOT NULL DEFAULT 1,
    MODIFY is_available       TINYINT(1) NOT NULL DEFAULT 1,
    MODIFY img_url            VARCHAR(255) NOT NULL DEFAULT '',
    ADD INDEX idx_products_category (category_id);

-- Products with Hot/Iced or size options: base price = cheapest option
UPDATE products p
JOIN (SELECT product_id, MIN(price) AS min_price FROM product_variants GROUP BY product_id) v
    ON v.product_id = p.product_id
SET p.price = v.min_price
WHERE p.price = 0;

-- ----------------------------------------------------- product_variants ----
ALTER TABLE product_variants
    MODIFY variant_type  VARCHAR(50) NULL,
    MODIFY size_label    VARCHAR(50) NULL,
    MODIFY is_available  TINYINT(1) NOT NULL DEFAULT 1;

-- --------------------------------------------------------------- orders ----
-- Enums become plain text; values (COD / QRPH, Pending / Paid / Refunded) are kept.
UPDATE orders SET payment_type = 'COD' WHERE payment_type IS NULL OR payment_type = '';

ALTER TABLE orders
    MODIFY user_id          INT NULL,
    MODIFY payment_status   VARCHAR(20) NOT NULL DEFAULT 'Pending',
    MODIFY payment_type     VARCHAR(20) NOT NULL DEFAULT 'COD',
    ADD COLUMN reference_number VARCHAR(50) NULL AFTER payment_type,
    MODIFY total_amount     DECIMAL(10,2) NOT NULL DEFAULT 0,
    MODIFY order_status     VARCHAR(20) NOT NULL DEFAULT 'Pending',
    MODIFY notes            TEXT NULL,
    ADD INDEX idx_orders_user (user_id);

-- ---------------------------------------------------------- order_items ----
-- IDs were UNSIGNED here but signed everywhere else; aligned so the foreign key works.
ALTER TABLE order_items
    MODIFY order_item_id  INT NOT NULL AUTO_INCREMENT,
    MODIFY order_id       INT NOT NULL,
    MODIFY product_id     INT NOT NULL,
    ADD COLUMN variant_type VARCHAR(50) NULL AFTER product_id,
    ADD COLUMN size_label   VARCHAR(50) NULL AFTER variant_type,
    MODIFY unit_price     DECIMAL(10,2) NOT NULL DEFAULT 0,
    ADD INDEX idx_order_items_product (product_id),
    ADD CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE;

-- ----------------------------------------------------------- new tables ----
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

SET FOREIGN_KEY_CHECKS = 1;

-- Done. Optional: if names show "&amp;" in the app, run once from the project folder:
--   php database/fix-escaped-text.php --apply
