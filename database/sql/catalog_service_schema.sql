-- Catalog Service Database Schema
-- Database: catalog_db

USE catalog_db;

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    slug VARCHAR(100) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Books Table (Core Catalog)
CREATE TABLE IF NOT EXISTS books (
    id INT PRIMARY KEY AUTO_INCREMENT,
    isbn VARCHAR(20) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category_id INT,
    publisher VARCHAR(255),
    publication_year INT,
    pages INT,
    language VARCHAR(50) DEFAULT 'English',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_category (category_id),
    INDEX idx_title (title),
    INDEX idx_isbn (isbn),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Categories Data
INSERT INTO categories (name, description, slug) VALUES
('Technology', 'Computer Science and Programming Books', 'technology'),
('Fiction', 'Novels and Literary Works', 'fiction'),
('Business', 'Business Management and Entrepreneurship', 'business'),
('Science', 'Science and Nature Studies', 'science')
ON DUPLICATE KEY UPDATE id=id;

-- Sample Books Data
INSERT INTO books (isbn, title, author, description, price, category_id, publisher, publication_year, pages) VALUES
('978-0262033848', 'Introduction to Algorithms', 'Thomas H. Cormen', 'A comprehensive guide to algorithms and data structures for computer scientists.', 89.99, 1, 'MIT Press', 2009, 1312),
('978-0132350884', 'Clean Code', 'Robert C. Martin', 'A Handbook of Agile Software Craftsmanship and best practices for writing maintainable code.', 37.50, 1, 'Prentice Hall', 2008, 464),
('978-0441172719', 'Dune', 'Frank Herbert', 'An epic science fiction novel set in a far future universe.', 14.99, 2, 'Ace', 1965, 688),
('978-0307887894', 'The Lean Startup', 'Eric Ries', 'How today\'s entrepreneurs use continuous innovation to create radically successful businesses.', 24.95, 3, 'Crown Business', 2011, 336),
('978-0553380163', 'A Brief History of Time', 'Stephen Hawking', 'A landmark volume explaining complex cosmological concepts in accessible language.', 18.99, 4, 'Bantam', 1988, 256),
('978-0374533557', 'Thinking, Fast and Slow', 'Daniel Kahneman', 'Explores the two systems that drive the way we think and make decisions.', 21.00, 4, 'Farrar Straus Giroux', 2011, 512)
ON DUPLICATE KEY UPDATE id=id;
