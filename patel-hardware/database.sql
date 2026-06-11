-- Patel Hardware Database Script
-- This script creates the database and the required tables

-- Create the database
CREATE DATABASE IF NOT EXISTS patel_hardware;
USE patel_hardware;

-- Create the users table to store registered users
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Create the items table to store hardware items
CREATE TABLE items (
    item_id INT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    color VARCHAR(50),
    description VARCHAR(255),
    price DECIMAL(10, 2) NOT NULL,
    available VARCHAR(3) DEFAULT 'Yes'
);

-- Create the cart table to store items added to cart by users
CREATE TABLE cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (item_id) REFERENCES items(item_id)
);

-- Insert sample hardware items
INSERT INTO items (item_id, name, color, description, price, available) VALUES
(1111, 'PPC Cement', 'White', '50 KG CEMENT BAG', 110.00, 'Yes'),
(1112, 'Duram Paint', 'White', '20L DURAM PAINT', 350.00, 'Yes'),
(1113, 'Drainage end cap', 'Black', 'DRAINAGE END CAP 110MM', 56.00, 'Yes'),
(1114, 'Ladder', 'White', 'ALUMINIUM EXTENSION LADDER 6M', 800.00, 'Yes'),
(1115, 'PPC Cement', 'White', '60 KG Cement bag', 115.00, 'Yes'),
(1116, 'Alluminium Door', 'Charcoal', 'KENZO ALUMINIUM ENTRANCE DOOR', 1350.00, 'Yes'),
(1117, 'Barge Board', 'Silver', 'BARGE BOARD CONNECTOR PVC', 560.00, 'Yes');
