-- schema.sql - Electronic Shopping Cart MySQL Database Schema

CREATE DATABASE IF NOT EXISTS `electro_cart` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `electro_cart`;

-- Categories Table
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) UNIQUE NOT NULL,
    `icon` VARCHAR(50),
    `description` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Products Table
CREATE TABLE IF NOT EXISTS `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) UNIQUE NOT NULL,
    `short_desc` VARCHAR(500),
    `description` TEXT,
    `specs` JSON,
    `price` DECIMAL(10, 2) NOT NULL,
    `old_price` DECIMAL(10, 2),
    `rating` DECIMAL(3, 1) DEFAULT 4.8,
    `reviews_count` INT DEFAULT 24,
    `stock` INT DEFAULT 15,
    `image_url` VARCHAR(255) NOT NULL,
    `badge` VARCHAR(50),
    `is_featured` TINYINT(1) DEFAULT 0,
    CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Orders Table
CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_number` VARCHAR(50) UNIQUE NOT NULL,
    `customer_name` VARCHAR(150) NOT NULL,
    `customer_email` VARCHAR(150) NOT NULL,
    `phone` VARCHAR(50),
    `address` TEXT NOT NULL,
    `city` VARCHAR(100),
    `zip` VARCHAR(20),
    `payment_method` VARCHAR(50) NOT NULL,
    `subtotal` DECIMAL(10, 2) NOT NULL,
    `discount` DECIMAL(10, 2) DEFAULT 0,
    `shipping` DECIMAL(10, 2) DEFAULT 0,
    `tax` DECIMAL(10, 2) DEFAULT 0,
    `total` DECIMAL(10, 2) NOT NULL,
    `status` VARCHAR(50) DEFAULT 'Confirmed',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Order Items Table
CREATE TABLE IF NOT EXISTS `order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `product_name` VARCHAR(255) NOT NULL,
    `price` DECIMAL(10, 2) NOT NULL,
    `quantity` INT NOT NULL,
    `total` DECIMAL(10, 2) NOT NULL,
    CONSTRAINT `fk_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
