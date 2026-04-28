-- ============================================================
-- iTradeZA C2C E-Commerce Platform - Database Schema
-- MySQL Database Setup Script
-- ============================================================

CREATE DATABASE IF NOT EXISTS itradeza_c2c
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE itradeza_c2c;

-- ------------------------------------------------------------
-- 1. Roles (RBAC)
-- ------------------------------------------------------------
CREATE TABLE roles (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(50)  NOT NULL UNIQUE,
    description VARCHAR(255) NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO roles (name, description) VALUES
('admin',  'Platform administrator with full access'),
('seller', 'Verified seller who can list and sell products'),
('buyer',  'Registered buyer who can browse and purchase');

-- ------------------------------------------------------------
-- 2. Users
-- ------------------------------------------------------------
CREATE TABLE users (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    username       VARCHAR(50)  NOT NULL UNIQUE,
    email          VARCHAR(100) NOT NULL UNIQUE,
    password_hash  VARCHAR(255) NOT NULL,
    first_name     VARCHAR(50)  NOT NULL,
    last_name      VARCHAR(50)  NOT NULL,
    phone          VARCHAR(20)  NULL,
    address        VARCHAR(255) NULL,
    city           VARCHAR(100) NULL,
    province       VARCHAR(100) NULL,
    role_id        INT          NOT NULL DEFAULT 3,
    is_verified    TINYINT(1)   NOT NULL DEFAULT 0,
    profile_image  VARCHAR(255) NULL,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 3. Categories
-- ------------------------------------------------------------
CREATE TABLE categories (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    description VARCHAR(255) NULL,
    parent_id   INT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO categories (name, description) VALUES
('Electronics',       'Phones, laptops, tablets, accessories'),
('Fashion & Clothing','Men\'s, women\'s and children\'s clothing'),
('Home & Garden',     'Furniture, décor, kitchen, garden'),
('Vehicles & Parts',  'Cars, motorcycles, spare parts'),
('Books & Education', 'Textbooks, stationery, courses'),
('Sports & Outdoors', 'Sports equipment, camping, fitness'),
('Beauty & Health',   'Skincare, makeup, wellness products'),
('Food & Groceries',  'Homemade food, fresh produce, spices'),
('Services',          'Freelance, repairs, tutoring'),
('Other',             'Anything that doesn\'t fit above');

-- ------------------------------------------------------------
-- 4. Products
-- ------------------------------------------------------------
CREATE TABLE products (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    seller_id   INT          NOT NULL,
    category_id INT          NOT NULL,
    title       VARCHAR(150) NOT NULL,
    description TEXT         NOT NULL,
    price       DECIMAL(10,2) NOT NULL,
    `condition` ENUM('new','used','refurbished') NOT NULL DEFAULT 'used',
    quantity    INT          NOT NULL DEFAULT 1,
    location    VARCHAR(150) NULL,
    status      ENUM('active','sold','inactive') NOT NULL DEFAULT 'active',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id)   REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 5. Product Images
-- ------------------------------------------------------------
CREATE TABLE product_images (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    product_id  INT          NOT NULL,
    image_path  VARCHAR(255) NOT NULL,
    is_primary  TINYINT(1)   NOT NULL DEFAULT 0,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 6. Shopping Cart
-- ------------------------------------------------------------
CREATE TABLE cart (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    buyer_id   INT NOT NULL,
    product_id INT NOT NULL,
    quantity   INT NOT NULL DEFAULT 1,
    added_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (buyer_id)   REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (buyer_id, product_id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 7. Orders
-- ------------------------------------------------------------
CREATE TABLE orders (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    buyer_id         INT            NOT NULL,
    seller_id        INT            NOT NULL,
    total_amount     DECIMAL(10,2)  NOT NULL,
    status           ENUM('pending','paid','shipped','delivered','cancelled')
                     NOT NULL DEFAULT 'pending',
    payment_method   VARCHAR(50)    NULL,
    shipping_address VARCHAR(255)   NULL,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (buyer_id)  REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 8. Order Items
-- ------------------------------------------------------------
CREATE TABLE order_items (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    order_id         INT            NOT NULL,
    product_id       INT            NOT NULL,
    quantity         INT            NOT NULL DEFAULT 1,
    price_at_purchase DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id)   REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 9. Messages (Buyer ↔ Seller)
-- ------------------------------------------------------------
CREATE TABLE messages (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    sender_id   INT  NOT NULL,
    receiver_id INT  NOT NULL,
    product_id  INT  NULL,
    message     TEXT NOT NULL,
    is_read     TINYINT(1) NOT NULL DEFAULT 0,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id)   REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id)  REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 10. Reviews / Ratings
-- ------------------------------------------------------------
CREATE TABLE reviews (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    reviewer_id      INT      NOT NULL,
    reviewed_user_id INT      NOT NULL,
    order_id         INT      NULL,
    rating           TINYINT  NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment          TEXT     NULL,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reviewer_id)      REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id)         REFERENCES orders(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Default admin user  (password: Admin@123)
-- ------------------------------------------------------------
INSERT INTO users (username, email, password_hash, first_name, last_name, role_id, is_verified)
VALUES ('admin', 'admin@itradeza.co.za',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'System', 'Admin', 1, 1);
