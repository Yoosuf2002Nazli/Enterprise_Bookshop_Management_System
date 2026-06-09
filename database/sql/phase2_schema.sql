-- ================================================================
-- BOOKSHOP MANAGEMENT SYSTEM — PHASE 2 DATABASE SCHEMA
-- Target Databases: user_db, catalog_db, inventory_db, order_db, notification_db
-- Compatibility: MySQL 5.7+ / 8.x
-- ================================================================

-- ----------------------------------------------------------------
-- 1. USER DATABASE
-- ----------------------------------------------------------------
USE user_db;

CREATE TABLE IF NOT EXISTS users (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  fullname      VARCHAR(100) NOT NULL,
  email         VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role          ENUM('customer','staff') NOT NULL DEFAULT 'customer',
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------
-- 2. CATALOG DATABASE
-- ----------------------------------------------------------------
USE catalog_db;

CREATE TABLE IF NOT EXISTS books (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(200) NOT NULL,
  author      VARCHAR(150) NOT NULL,
  isbn        VARCHAR(20) NOT NULL UNIQUE,
  category    ENUM('Technology','Fiction','Business','Science') NOT NULL,
  price       DECIMAL(8,2) NOT NULL,
  icon        VARCHAR(50) DEFAULT 'bi-book',
  icon_color  VARCHAR(50) DEFAULT 'text-primary',
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO books
  (title, author, isbn, category, price, icon, icon_color)
VALUES
  ('Introduction to Algorithms','Thomas H. Cormen',
   '978-0262033848','Technology',89.99,'bi-code-square','text-primary'),
  ('Clean Code','Robert C. Martin',
   '978-0132350884','Technology',37.50,'bi-terminal-fill','text-dark'),
  ('Dune','Frank Herbert',
   '978-0441172719','Fiction',14.99,'bi-compass-fill','text-warning'),
  ('The Lean Startup','Eric Ries',
   '978-0307887894','Business',24.95,'bi-graph-up-arrow','text-success'),
  ('A Brief History of Time','Stephen Hawking',
   '978-0553380163','Science',18.99,'bi-stars','text-info'),
  ('Thinking, Fast and Slow','Daniel Kahneman',
   '978-0374533557','Science',21.00,'bi-brain','text-danger');

-- ----------------------------------------------------------------
-- 3. INVENTORY DATABASE
-- ----------------------------------------------------------------
USE inventory_db;

CREATE TABLE IF NOT EXISTS inventory (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  book_id    INT UNSIGNED NOT NULL,
  isbn       VARCHAR(20) NOT NULL,
  title      VARCHAR(200) NOT NULL,
  category   VARCHAR(50) NOT NULL,
  stock      INT UNSIGNED NOT NULL DEFAULT 0,
  threshold  INT UNSIGNED NOT NULL DEFAULT 5,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO inventory
  (book_id, isbn, title, category, stock, threshold)
VALUES
  (1,'978-0262033848','Introduction to Algorithms','Technology',12,10),
  (2,'978-0132350884','Clean Code','Technology',3,8),
  (3,'978-0441172719','Dune','Fiction',25,5),
  (4,'978-0307887894','The Lean Startup','Business',0,5),
  (5,'978-0553380163','A Brief History of Time','Science',8,10),
  (6,'978-0374533557','Thinking, Fast and Slow','Science',15,10);

-- ----------------------------------------------------------------
-- 4. ORDER DATABASE
-- ----------------------------------------------------------------
USE order_db;

CREATE TABLE IF NOT EXISTS orders (
  id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_ref VARCHAR(20) NOT NULL UNIQUE,
  customer  VARCHAR(100) NOT NULL,
  email     VARCHAR(150) NOT NULL,
  total     DECIMAL(8,2) NOT NULL DEFAULT 0.00,
  status    ENUM('Pending','Shipped','Delivered','Cancelled') DEFAULT 'Pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS order_items (
  id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_ref VARCHAR(20) NOT NULL,
  title     VARCHAR(200) NOT NULL,
  qty       INT UNSIGNED NOT NULL DEFAULT 1,
  price     DECIMAL(8,2) NOT NULL,
  FOREIGN KEY (order_ref) REFERENCES orders(order_ref) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO orders
  (order_ref, customer, email, total, status)
VALUES
  ('ORD-2026-98101','Alice Vance',
   'alice@university.edu',127.49,'Pending'),
  ('ORD-2026-98102','Bob Miller',
   'bob.m@university.edu',29.98,'Shipped'),
  ('ORD-2026-98103','Charlie Stone',
   'cstone@university.edu',39.99,'Delivered');

INSERT IGNORE INTO order_items
  (order_ref, title, qty, price)
VALUES
  ('ORD-2026-98101','Introduction to Algorithms',1,89.99),
  ('ORD-2026-98101','Clean Code',1,37.50),
  ('ORD-2026-98102','Dune',2,14.99),
  ('ORD-2026-98103','A Brief History of Time',1,18.99),
  ('ORD-2026-98103','Thinking, Fast and Slow',1,21.00);

-- ----------------------------------------------------------------
-- 5. NOTIFICATION DATABASE
-- ----------------------------------------------------------------
USE notification_db;

CREATE TABLE IF NOT EXISTS notifications (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  type         VARCHAR(50) NOT NULL,
  message      TEXT NOT NULL,
  reference_id VARCHAR(50) DEFAULT NULL,
  created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
