-- Patel Hardware Database Schema
-- Creates the database and required tables with primary keys and foreign keys

CREATE DATABASE IF NOT EXISTS patel_hardware;
USE patel_hardware;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- Items table
CREATE TABLE IF NOT EXISTS items (
    item_id INT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    color VARCHAR(50),
    description VARCHAR(255),
    price DECIMAL(10,2) NOT NULL,
    available VARCHAR(3) DEFAULT 'Yes'
) ENGINE=InnoDB;

-- Cart table (foreign keys reference users and items)
CREATE TABLE IF NOT EXISTS cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Insert sample items (matching the Patel Hardware inventory)
INSERT INTO items (item_id, name, color, description, price, available) VALUES
(1111, 'PPC Cement', 'White', '50 KG CEMENT BAG', 110.00, 'Yes'),
(1112, 'Duram Paint', 'White', '20L DURAM PAINT', 350.00, 'Yes'),
(1113, 'Drainage end cap', 'Black', 'DRAINAGE END CAP 110MM', 56.00, 'Yes'),
(1114, 'Ladder', 'White', 'ALUMINIUM EXTENSION LADDER 6M', 800.00, 'Yes'),
(1115, 'PPC Cement', 'White', '60 KG Cement bag', 115.00, 'Yes'),
(1116, 'Aluminium Door', 'Charcoal', 'KENZO ALUMINIUM ENTRANCE DOOR', 1350.00, 'Yes'),
(1117, 'Barge Board', 'Silver', 'BARGE BOARD CONNECTOR PVC', 560.00, 'Yes');
