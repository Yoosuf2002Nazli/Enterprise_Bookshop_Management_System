# Chapter 4: Database Design & Decoupling

## 4.1 Database-per-Service Architecture
To achieve decoupling, the Bookshop Management System utilizes a **Database-per-Service** design. Each microservice owns and communicates with its respective database on port `3306`:

1. `user_db` -> owned by **User Service**
2. `catalog_db` -> owned by **Catalog Service**
3. `inventory_db` -> owned by **Inventory Service**
4. `order_db` -> owned by **Order Service**
5. `notification_db` -> owned by **Notification Service**

No service is allowed to query another service's database directly. 

---

## 4.2 Entity Relational Schema Definitions

### 4.2.1 `user_db.users`
Stores user records. Password columns store secure hashes generated using PHP's standard `password_hash()` (bcrypt).
* `id` (INT, PK, Auto-Increment)
* `fullname` (VARCHAR)
* `email` (VARCHAR, Unique)
* `password` (VARCHAR)
* `role` (VARCHAR)
* `created_at` (TIMESTAMP)

### 4.2.2 `catalog_db.books`
Houses book catalog listings.
* `id` (INT, PK, Auto-Increment)
* `title` (VARCHAR)
* `author` (VARCHAR)
* `isbn` (VARCHAR, Unique)
* `category` (VARCHAR)
* `price` (DECIMAL)
* `icon` (VARCHAR)
* `icon_color` (VARCHAR)
* `created_at` (TIMESTAMP)

### 4.2.3 `inventory_db.inventory`
Tracks stock levels. Mapped using `isbn` and `book_id` references.
* `id` (INT, PK, Auto-Increment)
* `book_id` (INT)
* `isbn` (VARCHAR, Unique)
* `title` (VARCHAR)
* `category` (VARCHAR)
* `stock` (INT)
* `threshold` (INT)
* `updated_at` (TIMESTAMP)

### 4.2.4 `order_db.orders`
Records sales logs. The ordered books are serialized as a JSON string inside the `items` column.
* `id` (INT, PK, Auto-Increment)
* `order_ref` (VARCHAR, Unique)
* `customer` (VARCHAR)
* `email` (VARCHAR)
* `total` (DECIMAL)
* `status` (VARCHAR)
* `items` (TEXT)
* `created_at` (TIMESTAMP)

### 4.2.5 `notification_db.notifications`
Contains transaction logs and system alerts.
* `id` (INT, PK, Auto-Increment)
* `type` (VARCHAR)
* `message` (TEXT)
* `reference_id` (VARCHAR)
* `created_at` (TIMESTAMP)

---

## 4.3 Relational Mechanics & Application Joins
Because cross-database constraints are avoided, data consistency is maintained at the application level. During checkout:
1. The Order Service verifies item availability by calling the Inventory Service.
2. If stock is available, it makes an API call to reduce stock.
3. If stock reduction succeeds, the order is created and saved.
4. An alert is sent to the Notification Service to log the event.
5. If any database write fails during this flow, the transaction is cancelled and an error is returned.
