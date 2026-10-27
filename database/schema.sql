-- Daily Spot Cafe database schema
-- Reconstructed from the queries in src/model/*.php.
-- Usage: mysql -u root -p < database/schema.sql

CREATE DATABASE IF NOT EXISTS daily_spot_cafe
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE daily_spot_cafe;

-- The code checks role_id directly (1 = admin); this table names the values.
CREATE TABLE IF NOT EXISTS roles (
    role_id    INT PRIMARY KEY,
    role_name  VARCHAR(45) NOT NULL
);
INSERT IGNORE INTO roles (role_id, role_name) VALUES (1, 'Admin'), (2, 'Customer');

CREATE TABLE IF NOT EXISTS users (
    user_id     INT AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(100) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    role_id     INT NOT NULL DEFAULT 2,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(role_id)
);

-- One row per user; user_id mirrors users.user_id.
CREATE TABLE IF NOT EXISTS user_details (
    user_id       INT NOT NULL PRIMARY KEY,
    first_name    VARCHAR(100) DEFAULT '',
    middle_name   VARCHAR(100) DEFAULT '',
    last_name     VARCHAR(100) DEFAULT '',
    suffix        VARCHAR(20)  DEFAULT '',
    phone_number  VARCHAR(30)  DEFAULT '',
    email         VARCHAR(255) DEFAULT '',
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS categories (
    category_id    INT AUTO_INCREMENT PRIMARY KEY,
    category_name  VARCHAR(100) NOT NULL,
    description    TEXT,
    is_active      TINYINT(1) NOT NULL DEFAULT 1,
    has_variants   TINYINT(1) NOT NULL DEFAULT 0, -- Iced/Hot
    has_sizes      TINYINT(1) NOT NULL DEFAULT 0, -- Grande/Venti
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    product_id         INT AUTO_INCREMENT PRIMARY KEY,
    product_name       VARCHAR(150) NOT NULL,
    price              DECIMAL(10,2) NOT NULL DEFAULT 0,
    short_description  VARCHAR(255) DEFAULT '',
    long_description   TEXT,
    category_id        INT,
    is_published       TINYINT(1) NOT NULL DEFAULT 1,
    is_available       TINYINT(1) NOT NULL DEFAULT 1,
    img_url            VARCHAR(255) DEFAULT '',
    created_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (category_id)
);

CREATE TABLE IF NOT EXISTS product_variants (
    variant_id    INT AUTO_INCREMENT PRIMARY KEY,
    product_id    INT NOT NULL,
    variant_type  VARCHAR(50) NULL,  -- 'Iced', 'Hot', or NULL
    size_label    VARCHAR(50) NULL,  -- 'Grande', 'Venti', or NULL
    price         DECIMAL(10,2) NOT NULL DEFAULT 0,
    is_available  TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

-- payment_status: Pending | Paid | Refunded
-- payment_type:   COD | QRPH  (shown as "Cash on Pickup" and "QR PH")
-- order_status:   Pending | Processing | Ready | Completed | Cancelled
CREATE TABLE IF NOT EXISTS orders (
    order_id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id           INT,
    payment_status    VARCHAR(20) NOT NULL DEFAULT 'Pending',
    payment_type      VARCHAR(20) NOT NULL DEFAULT 'COD',
    reference_number  VARCHAR(50) NULL, -- QR PH payment reference
    total_amount    DECIMAL(10,2) NOT NULL DEFAULT 0,
    order_status    VARCHAR(20) NOT NULL DEFAULT 'Pending',
    notes           TEXT,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (user_id)
);

CREATE TABLE IF NOT EXISTS order_items (
    order_item_id  INT AUTO_INCREMENT PRIMARY KEY,
    order_id       INT NOT NULL,
    product_id     INT NOT NULL,
    variant_type   VARCHAR(50) NULL,
    size_label     VARCHAR(50) NULL,
    quantity       INT NOT NULL DEFAULT 1,
    unit_price     DECIMAL(10,2) NOT NULL DEFAULT 0,
    INDEX (product_id),
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE
);

-- Also auto-created by src/model/Notification.php
CREATE TABLE IF NOT EXISTS notifications (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(255) NOT NULL,
    message     TEXT NOT NULL,
    type        VARCHAR(50) DEFAULT 'info',
    is_read     TINYINT(1) DEFAULT 0,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Contact form submissions
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
