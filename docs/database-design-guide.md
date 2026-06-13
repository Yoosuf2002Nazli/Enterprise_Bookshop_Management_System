# Bookshop Management System — Database Design Guide

This guide describes the database architecture for the Bookshop Management System. The project adheres to strict microservice data isolation guidelines—**there are no cross-database foreign key constraints or SQL JOINs**. All relation correlations are resolved at the application layer.

---

## 1. Database Configuration Overview
* **Database Engine:** MySQL / MariaDB (Port `3306`)
* **Databases:** 5 isolated database catalogs, one per service:
  1. `user_db` — Auth credentials and account metadata.
  2. `catalog_db` — Catalog information.
  3. `inventory_db` — Stock level indicators.
  4. `order_db` — Sales invoices and transactions.
  5. `notification_db` — Alerts and notification log records.

---

## 2. Service Database Schemas

### A. User Service Database (`user_db`)
Contains account roles and credentials.

#### Table: `users`
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | `PRIMARY KEY AUTO_INCREMENT` | Unique identifier. |
| `fullname`| `VARCHAR(100)` | `NOT NULL` | User's full name. |
| `email` | `VARCHAR(100)` | `NOT NULL UNIQUE` | Account email (used for login). |
| `password`| `VARCHAR(255)` | `NOT NULL` | Hashed password (using standard bcrypt). |
| `role` | `VARCHAR(20)` | `DEFAULT 'customer'` | Permission roles: `customer`, `staff`, `admin`. |
| `created_at`| `TIMESTAMP` | `DEFAULT CURRENT_TIMESTAMP` | Account creation timestamp. |

---

### B. Catalog Service Database (`catalog_db`)
Houses catalog book records.

#### Table: `books`
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | `PRIMARY KEY AUTO_INCREMENT` | Unique book ID. |
| `title` | `VARCHAR(150)` | `NOT NULL` | Title of the publication. |
| `author` | `VARCHAR(100)` | `NOT NULL` | Writer name. |
| `isbn` | `VARCHAR(20)` | `NOT NULL UNIQUE` | Industry ISBN code. |
| `category`| `VARCHAR(50)` | `NOT NULL` | Genre (Technology, Science, etc.). |
| `price` | `DECIMAL(10,2)`| `NOT NULL` | Retail price. |
| `icon` | `VARCHAR(50)` | `DEFAULT 'bi-book'` | Bootstrap Icon name. |
| `icon_color`| `VARCHAR(30)` | `DEFAULT 'text-dark'`| CSS text color class. |
| `created_at`| `TIMESTAMP` | `DEFAULT CURRENT_TIMESTAMP` | Book record creation timestamp. |

---

### C. Inventory Service Database (`inventory_db`)
Tracks stock counts and low-stock warning triggers.

#### Table: `inventory`
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | `PRIMARY KEY AUTO_INCREMENT` | Unique inventory record ID. |
| `book_id` | `INT` | `NOT NULL` | Core identifier matching book catalog. |
| `isbn` | `VARCHAR(20)` | `NOT NULL UNIQUE` | Reference key matching catalog books. |
| `title` | `VARCHAR(150)` | `NOT NULL` | Cached book title for local performance. |
| `category`| `VARCHAR(50)` | `NOT NULL` | Book category. |
| `stock` | `INT` | `NOT NULL DEFAULT 0` | Available items count. |
| `threshold`| `INT` | `NOT NULL DEFAULT 5` | Safety replenishment threshold limit. |
| `updated_at`| `TIMESTAMP` | `ON UPDATE CURRENT_TIMESTAMP` | Last stock update timestamp. |

---

### D. Order Service Database (`order_db`)
Maintains sales receipts and transaction states.

#### Table: `orders`
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | `PRIMARY KEY AUTO_INCREMENT` | Unique invoice key. |
| `order_ref`| `VARCHAR(30)` | `NOT NULL UNIQUE` | String reference code (e.g. `ORD-XXXXX`). |
| `customer` | `VARCHAR(100)` | `NOT NULL` | Customer's full name. |
| `email` | `VARCHAR(100)` | `NOT NULL` | Customer email. |
| `total` | `DECIMAL(10,2)`| `NOT NULL` | Order total amount. |
| `status` | `VARCHAR(20)` | `DEFAULT 'Pending'` | Pipeline: `Pending`, `Shipped`, `Delivered`, `Cancelled`. |
| `items` | `TEXT` | `NOT NULL` | JSON array dump of ordered items. |
| `created_at`| `TIMESTAMP` | `DEFAULT CURRENT_TIMESTAMP` | Purchase timestamp. |

---

### E. Notification Service Database (`notification_db`)
Records system logs and low-stock warning histories.

#### Table: `notifications`
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | `PRIMARY KEY AUTO_INCREMENT` | Unique notification ID. |
| `type` | `VARCHAR(50)` | `NOT NULL` | Category: `Order Placement`, `Low Stock Warning`. |
| `message` | `TEXT` | `NOT NULL` | Alert description message. |
| `reference_id`| `VARCHAR(50)` | `NULL` | Optional code relation (ISBN or order ref). |
| `created_at`| `TIMESTAMP` | `DEFAULT CURRENT_TIMESTAMP` | Alert log timestamp. |

---

## 3. Relational Coordination (Application Layer Joins)
Since databases are isolated, standard `JOIN` operations are not possible. Relations are computed in code:
1. **Frontend Catalog Display:** The frontend calls the Catalog Service to fetch all books, and queries the Inventory Service to append the corresponding stock level matching each book's ID or ISBN.
2. **Order Placement Stock Deduction:** During checkout, the Order Service performs a backend cURL request to `inventory-service/api/inventory.php?action=reduce` sending the target `isbn` and `qty`. If the stock reduction succeeds, the order is saved; otherwise, the transaction is aborted.
