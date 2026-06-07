-- Inventory Service Database Schema
-- Database: inventory_db

USE inventory_db;

-- Warehouses Table (Stock Locations)
CREATE TABLE IF NOT EXISTS warehouses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    location VARCHAR(255),
    manager_email VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Stock Levels Table (Core Inventory)
CREATE TABLE IF NOT EXISTS stock_levels (
    id INT PRIMARY KEY AUTO_INCREMENT,
    book_id INT NOT NULL,
    warehouse_id INT NOT NULL,
    quantity_on_hand INT NOT NULL DEFAULT 0,
    reorder_point INT NOT NULL DEFAULT 10,
    reorder_quantity INT NOT NULL DEFAULT 20,
    last_restocked_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_book_warehouse (book_id, warehouse_id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE CASCADE,
    INDEX idx_book (book_id),
    INDEX idx_quantity (quantity_on_hand)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Stock Transactions Log (Audit Trail)
CREATE TABLE IF NOT EXISTS stock_transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    book_id INT NOT NULL,
    warehouse_id INT NOT NULL,
    transaction_type ENUM('IN', 'OUT', 'ADJUSTMENT', 'LOSS') NOT NULL,
    quantity INT NOT NULL,
    reason VARCHAR(255),
    reference_id VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_book (book_id),
    INDEX idx_warehouse (warehouse_id),
    INDEX idx_type (transaction_type),
    INDEX idx_date (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Low Stock Alerts Table
CREATE TABLE IF NOT EXISTS low_stock_alerts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    book_id INT NOT NULL,
    warehouse_id INT NOT NULL,
    alert_status ENUM('PENDING', 'ACKNOWLEDGED', 'RESOLVED') DEFAULT 'PENDING',
    current_stock INT NOT NULL,
    reorder_point INT NOT NULL,
    notified_at TIMESTAMP NULL,
    resolved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (alert_status),
    INDEX idx_book_warehouse (book_id, warehouse_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Warehouse Data
INSERT INTO warehouses (name, location, manager_email) VALUES
('Main Warehouse', 'Downtown Distribution Center', 'manager@warehouse.edu'),
('Secondary Storage', 'East Side Facility', 'secondary@warehouse.edu')
ON DUPLICATE KEY UPDATE id=id;

-- Sample Stock Levels (Initial inventory for all books)
INSERT INTO stock_levels (book_id, warehouse_id, quantity_on_hand, reorder_point, reorder_quantity) VALUES
(1, 1, 12, 10, 20),
(2, 1, 3, 8, 15),
(3, 1, 25, 5, 25),
(4, 1, 0, 5, 20),
(5, 1, 8, 10, 15),
(6, 1, 15, 10, 20),
-- Secondary warehouse stock
(1, 2, 8, 10, 20),
(2, 2, 5, 8, 15),
(3, 2, 18, 5, 25)
ON DUPLICATE KEY UPDATE id=id;
