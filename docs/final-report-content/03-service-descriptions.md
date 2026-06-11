# Chapter 3: Service Descriptions

This chapter details the codebase structures, models, controllers, and endpoints of the five decoupled microservices.

---

## 3.1 User Authentication Service (Port 8001)
* **Functional Scope:** Registers accounts, validates passwords, and returns user identity metadata.
* **Component Structures:**
  * **Model (`UserModel.php`):** Queries `user_db.users` via PDO. Handles bcrypt password hashing.
  * **Controller (`AuthController.php`):** Validates required inputs (fullname, unique email, password strength) and returns structured payloads.
  * **API Router (`auth.php`):** Maps query actions (register, login, logout) to corresponding controller actions.

---

## 3.2 Catalog Service (Port 8002)
* **Functional Scope:** Houses, queries, and filters bookstore catalogs.
* **Component Structures:**
  * **Model (`BookModel.php`):** Executes queries against `catalog_db.books`.
  * **Controller (`BookController.php`):** Implements catalog search validations.
  * **API Router (`books.php`):** Handles book retrieval, updates, and creation requests.

---

## 3.3 Inventory Service (Port 8003)
* **Functional Scope:** Tracks stock levels, triggers low-stock alerts, and processes replenishment requests.
* **Component Structures:**
  * **Model (`InventoryModel.php`):** Manages `inventory_db.inventory` table.
  * **Controller (`InventoryController.php`):** Tracks safety thresholds.
  * **API Router (`inventory.php`):** Exposes endpoints to reduce stock on purchase and restock items.

---

## 3.4 Order Service (Port 8004)
* **Functional Scope:** Orchestrates checkouts, manages statuses, and computes sales statistics.
* **Component Structures:**
  * **Model (`OrderModel.php`):** Saves transactions in `order_db.orders` using serialized JSON fields.
  * **Controller (`OrderController.php`):** Directs stock checkouts, status transitions, and soft cancellations.
  * **API Router (`orders.php`):** Exposes checkout and invoicing endpoints.

---

## 3.5 Notification Service (Port 8005)
* **Functional Scope:** Logs transaction histories, alerts, and system health status.
* **Component Structures:**
  * **Model (`NotificationModel.php`):** Writes logs to `notification_db.notifications`.
  * **Controller (`NotificationController.php`):** Structures system notifications.
  * **API Router (`notify.php`):** Exposes query/insert endpoints for event logs.
