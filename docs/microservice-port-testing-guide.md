# Microservice Port Testing & Deployment Guide

This guide describes the transition of the Bookshop Management System from a monolithic runtime setup to an independent microservice-port architecture. It outlines the testing procedures, endpoints, configuration mappings, and start/stop instructions for development and milestones validation.

---

## 1. Architectural Transition: Single Apache vs. Dedicated Ports

### Previous Architecture
Previously, all services (`user-service`, `catalog-service`, `inventory-service`, `order-service`, `notification-service`) were served under a single Apache HTTP server instance (e.g., using XAMPP) on a single port (usually port `80` or `8080`). While this allowed simple development, it did not satisfy the strict microservices isolation requirements since all services shared a runtime context, and calls to endpoints were routed through folders rather than network boundaries.

### New Architecture
Each microservice now runs on its own isolated PHP runtime instance on a dedicated TCP port. This mimics a true distributed architecture:
- **Port-level Isolation:** Services run as distinct HTTP endpoints, allowing independent configuration, deployment scaling, and debugging.
- **Independent Database Connections:** Each service manages its own connections to its respective MySQL database.
- **Postman & External API Testability:** API testing can be performed on discrete ports, proving decoupling at the transport layer.

---

## 2. Port & Service Mapping Table

The following table maps each microservice to its designated port, base URL, and associated MySQL database:

| Service Name | Port | Base API URL | Database Name |
| :--- | :--- | :--- | :--- |
| **User Service** | `8001` | `http://localhost:8001/api/auth.php` | `user_db` |
| **Catalog Service** | `8002` | `http://localhost:8002/api/books.php` | `catalog_db` |
| **Inventory Service** | `8003` | `http://localhost:8003/api/inventory.php` | `inventory_db` |
| **Order Service** | `8004` | `http://localhost:8004/api/orders.php` | `order_db` |
| **Notification Service**| `8005` | `http://localhost:8005/api/notify.php` | `notification_db` |

---

## 3. Microservice REST Endpoints Reference

Below are the detailed REST endpoints for each of the five services, including HTTP methods, query params, request payloads, and expected JSON responses.

---

### A. User Service (Port 8001)

Manages registration, login, and user management.

#### 1. Register User
*   **Method:** `POST`
*   **URL:** `http://localhost:8001/api/auth.php?action=register`
*   **Body (JSON):**
    ```json
    {
      "fullname": "John Doe",
      "email": "johndoe@example.com",
      "password": "securepassword",
      "confirm_password": "securepassword",
      "role": "customer"
    }
    ```
*   **Expected Response (201 Created):**
    ```json
    {
      "status": "success",
      "message": "User registered successfully."
    }
    ```

#### 2. Login User
*   **Method:** `POST`
*   **URL:** `http://localhost:8001/api/auth.php?action=login`
*   **Body (JSON):**
    ```json
    {
      "email": "johndoe@example.com",
      "password": "securepassword"
    }
    ```
*   **Expected Response (200 OK):**
    ```json
    {
      "status": "success",
      "message": "Login successful.",
      "email": "johndoe@example.com",
      "role": "customer"
    }
    ```

#### 3. Logout User
*   **Method:** `GET` or `POST`
*   **URL:** `http://localhost:8001/api/auth.php?action=logout`
*   **Expected Response (200 OK):**
    ```json
    {
      "status": "success",
      "message": "Logout successful."
    }
    ```

#### 4. Get All Users
*   **Method:** `GET`
*   **URL:** `http://localhost:8001/api/auth.php`
*   **Expected Response (200 OK):**
    ```json
    {
      "status": "success",
      "data": [
        {
          "id": 1,
          "fullname": "Admin User",
          "email": "admin@bookshop.com",
          "role": "staff",
          "created_at": "2026-06-07 10:00:00"
        }
      ]
    }
    ```

#### 5. Get User by ID
*   **Method:** `GET`
*   **URL:** `http://localhost:8001/api/auth.php?id=1`
*   **Expected Response (200 OK):**
    ```json
    {
      "status": "success",
      "data": {
        "id": 1,
        "fullname": "Admin User",
        "email": "admin@bookshop.com",
        "role": "staff",
        "created_at": "2026-06-07 10:00:00"
      }
    }
    ```

#### 6. Update User
*   **Method:** `PUT`
*   **URL:** `http://localhost:8001/api/auth.php?id=1`
*   **Body (JSON):**
    ```json
    {
      "fullname": "Updated Admin",
      "email": "admin@bookshop.com",
      "role": "staff"
    }
    ```
*   **Expected Response (200 OK):**
    ```json
    {
      "status": "success",
      "message": "User updated successfully.",
      "data": {
        "id": 1,
        "fullname": "Updated Admin",
        "email": "admin@bookshop.com",
        "role": "staff",
        "created_at": "2026-06-07 10:00:00"
      }
    }
    ```

#### 7. Delete User
*   **Method:** `DELETE`
*   **URL:** `http://localhost:8001/api/auth.php?id=1`
*   **Expected Response (200 OK):**
    ```json
    {
      "status": "success",
      "message": "User deleted successfully."
    }
    ```

---

### B. Catalog Service (Port 8002)

Manages book inventory items search, filters, and records.

#### 1. Get All Books
*   **Method:** `GET`
*   **URL:** `http://localhost:8002/api/books.php`
*   **Query Params (Optional):** `category` (e.g. `Fiction`), `search` (e.g. `Gatsby`)
*   **Expected Response (200 OK):**
    ```json
    {
      "status": "success",
      "data": [
        {
          "id": 1,
          "title": "The Great Gatsby",
          "author": "F. Scott Fitzgerald",
          "isbn": "9780743273565",
          "price": 10.99,
          "category": "Fiction"
        }
      ]
    }
    ```

#### 2. Get Book by ID
*   **Method:** `GET`
*   **URL:** `http://localhost:8002/api/books.php?id=1`
*   **Expected Response (200 OK):**
    ```json
    {
      "status": "success",
      "data": {
        "id": 1,
        "title": "The Great Gatsby",
        "author": "F. Scott Fitzgerald",
        "isbn": "9780743273565",
        "price": 10.99,
        "category": "Fiction"
      }
    }
    ```

#### 3. Create Book
*   **Method:** `POST`
*   **URL:** `http://localhost:8002/api/books.php`
*   **Body (JSON):**
    ```json
    {
      "title": "To Kill a Mockingbird",
      "author": "Harper Lee",
      "isbn": "9780061120084",
      "price": 14.99,
      "category": "Fiction"
    }
    ```
*   **Expected Response (201 Created):**
    ```json
    {
      "status": "success",
      "message": "Book successfully created."
    }
    ```

#### 4. Update Book
*   **Method:** `PUT`
*   **URL:** `http://localhost:8002/api/books.php?id=1`
*   **Body (JSON):**
    ```json
    {
      "title": "The Great Gatsby - Special Edition",
      "author": "F. Scott Fitzgerald",
      "isbn": "9780743273565",
      "price": 12.99,
      "category": "Fiction"
    }
    ```
*   **Expected Response (200 OK):**
    ```json
    {
      "status": "success",
      "message": "Book successfully updated."
    }
    ```

#### 5. Delete Book
*   **Method:** `DELETE`
*   **URL:** `http://localhost:8002/api/books.php?id=1`
*   **Expected Response (200 OK):**
    ```json
    {
      "status": "success",
      "message": "Book successfully deleted."
    }
    ```

---

### C. Inventory Service (Port 8003)

Manages item stock levels, warnings, and replenishments.

#### 1. Get All Inventory
*   **Method:** `GET`
*   **URL:** `http://localhost:8003/api/inventory.php`
*   **Query Params (Optional):** `filter=low` (to filter items below safety threshold)
*   **Expected Response (200 OK):**
    ```json
    {
      "status": "success",
      "data": [
        {
          "id": 1,
          "book_id": 1,
          "isbn": "9780743273565",
          "title": "The Great Gatsby",
          "category": "Fiction",
          "stock": 15,
          "threshold": 5,
          "updated_at": "2026-06-07 10:00:00"
        }
      ]
    }
    ```

#### 2. Get Inventory by ID
*   **Method:** `GET`
*   **URL:** `http://localhost:8003/api/inventory.php?id=1`
*   **Expected Response (200 OK):**
    ```json
    {
      "status": "success",
      "data": {
        "id": 1,
        "book_id": 1,
        "isbn": "9780743273565",
        "title": "The Great Gatsby",
        "category": "Fiction",
        "stock": 15,
        "threshold": 5,
        "updated_at": "2026-06-07 10:00:00"
      }
    }
    ```

#### 3. Restock Item (Replenish)
*   **Method:** `GET` or `POST` or `PUT`
*   **URL:** `http://localhost:8003/api/inventory.php?action=restock&id=1&qty=10`
*   **Expected Response (200 OK):**
    ```json
    {
      "status": "success",
      "message": "Stock successfully replenished."
    }
    ```

#### 4. Reduce Stock (Triggered on Checkout)
*   **Method:** `POST`
*   **URL:** `http://localhost:8003/api/inventory.php?action=reduce`
*   **Body (JSON):**
    ```json
    {
      "isbn": "9780743273565",
      "qty": 2
    }
    ```
*   **Expected Response (200 OK):**
    ```json
    {
      "status": "success",
      "message": "Stock successfully reduced."
    }
    ```

#### 5. Delete Inventory Record
*   **Method:** `DELETE`
*   **URL:** `http://localhost:8003/api/inventory.php?id=1`
*   **Expected Response (200 OK):**
    ```json
    {
      "status": "success",
      "message": "Inventory record deleted successfully."
    }
    ```

---

### D. Order Service (Port 8004)

Manages checking out orders, tracking statuses, and revenue computation.

#### 1. Place Order (Checkout)
*   **Method:** `POST`
*   **URL:** `http://localhost:8004/api/orders.php?action=create`
*   **Body (JSON):**
    ```json
    {
      "customer": "John Doe",
      "email": "johndoe@example.com",
      "book_id": 1,
      "book_title": "The Great Gatsby",
      "qty": 1,
      "price": 10.99
    }
    ```
*   **Expected Response (201 Created):**
    ```json
    {
      "status": "success",
      "message": "Order created successfully.",
      "order_ref": "ORD-2026-85712"
    }
    ```

#### 2. Get All Orders
*   **Method:** `GET`
*   **URL:** `http://localhost:8004/api/orders.php`
*   **Expected Response (200 OK):**
    ```json
    {
      "status": "success",
      "data": [
        {
          "id": 1,
          "order_ref": "ORD-2026-85712",
          "customer": "John Doe",
          "email": "johndoe@example.com",
          "total": 10.99,
          "status": "Pending",
          "items": "[{\"title\":\"The Great Gatsby\",\"qty\":1,\"price\":10.99}]",
          "created_at": "2026-06-11 12:40:00"
        }
      ]
    }
    ```

#### 3. Get Order by ID
*   **Method:** `GET`
*   **URL:** `http://localhost:8004/api/orders.php?id=1`
*   **Expected Response (200 OK):**
    ```json
    {
      "status": "success",
      "data": {
        "id": 1,
        "order_ref": "ORD-2026-85712",
        "customer": "John Doe",
        "email": "johndoe@example.com",
        "total": 10.99,
        "status": "Pending",
        "items": "[{\"title\":\"The Great Gatsby\",\"qty\":1,\"price\":10.99}]",
        "created_at": "2026-06-11 12:40:00"
      }
    }
    ```

#### 4. Transition Order Status
*   **Method:** `GET`
*   **URL:** `http://localhost:8004/api/orders.php?action=update_status&id=ORD-2026-85712&status=Shipped`
*   **Expected Response (200 OK):**
    ```json
    {
      "status": "success",
      "message": "Order status successfully transitioned."
    }
    ```

#### 5. Delete/Cancel Order
*   **Method:** `DELETE`
*   **URL:** `http://localhost:8004/api/orders.php?id=1`
*   **Expected Response (200 OK):**
    ```json
    {
      "status": "success",
      "message": "Order cancelled successfully."
    }
    ```

---

### E. Notification Service (Port 8005)

Manages system alerts and logs.

#### 1. Log Notification Alert
*   **Method:** `POST`
*   **URL:** `http://localhost:8005/api/notify.php?action=log`
*   **Body (JSON):**
    ```json
    {
      "type": "Low Stock Alert",
      "message": "The Great Gatsby has fallen below the safety threshold.",
      "reference_id": "9780743273565"
    }
    ```
*   **Expected Response (201 Created):**
    ```json
    {
      "status": "success",
      "message": "Notification log recorded successfully."
    }
    ```

#### 2. Get All Notification Logs
*   **Method:** `GET`
*   **URL:** `http://localhost:8005/api/notify.php`
*   **Query Params (Optional):** `limit=10`
*   **Expected Response (200 OK):**
    ```json
    {
      "status": "success",
      "data": [
        {
          "id": 1,
          "type": "Low Stock Alert",
          "message": "The Great Gatsby has fallen below the safety threshold.",
          "reference_id": "9780743273565",
          "created_at": "2026-06-11 12:42:00"
        }
      ]
    }
    ```

#### 3. Delete Notification Log
*   **Method:** `DELETE`
*   **URL:** `http://localhost:8005/api/notify.php?id=1`
*   **Expected Response (200 OK):**
    ```json
    {
      "status": "success",
      "message": "Notification log deleted successfully."
    }
    ```

---

## 4. Startup & Shutdown Operations Guide

### Starting the Microservices
A batch script has been provided at the project's root folder `tools/start-microservices.bat` to streamline launching the 5 services.

1.  Open **Command Prompt** or **PowerShell** and navigate to the project directory.
2.  Execute the script:
    ```cmd
    .\tools\start-microservices.bat
    ```
3.  The script will open **5 separate terminal windows**, one for each PHP server, running on ports `8001`, `8002`, `8003`, `8004`, and `8005`.
4.  Leave these terminal windows open to allow the services to listen for incoming API requests.

### Shutting Down the Microservices
To stop listening:
-   **Method 1:** Manually close each of the 5 separate terminal command windows.
-   **Method 2:** Open a terminal window and execute:
    ```cmd
    taskkill /F /IM php.exe
    ```
    *(Note: This forcefully closes all running PHP CLI built-in server processes on the host machine).*

---

## 5. Architectural & Dev Server Limitations

> [!WARNING]
> The PHP built-in web server (`php -S`) has constraints that developers should be aware of:
> 1. **Single-Threaded Processing:** The server runs in a single-threaded loop. It handles only one request at a time. Concurrency is not supported, meaning long-running queries will block other requests.
> 2. **Security Vulnerabilities:** It is meant for local staging and testing only. It lacks robust security policies, logging capacity, and production-grade connection limiting. **Never run `php -S` in a production environment.**
> 3. **Static Resource Handling:** It serves static files (images, css, js) basic-level, but it is not optimized for caching or compression headers.
