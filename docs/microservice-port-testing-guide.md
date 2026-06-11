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

### How the App Functions Without Apache (Beginner-Friendly Analogy)
If you are new to web servers, removing the Apache web server might feel confusing. Here is a simple way to understand how the system works now:

*   **Before (Single Apache Server):** Think of the project like a large department store. All services (Users, Catalog, Inventory, etc.) were different departments inside the **same building** (served on a single port like `80` or `8080` by Apache). If the building collapsed (Apache stopped), the entire store went down. Also, to edit files, you had to move your folder to `C:\xampp\htdocs\`.
*   **Now (Independent Dedicated Ports):** Think of the project like a street of **separate, independent shops** next to each other:
    *   **User Service** is a shop located at address `http://localhost:8001`.
    *   **Catalog Service** is a shop located at address `http://localhost:8002`.
    *   **Inventory Service** is a shop located at address `http://localhost:8003`.
    *   **Order Service** is a shop located at address `http://localhost:8004`.
    *   **Notification Service** is a shop located at address `http://localhost:8005`.
    *   **Frontend (UI)** is the customer facing website that communicates with these shops via their direct addresses.
    
#### Key Benefits for You:
1.  **No More Copying to `htdocs`!** Since each microservice runs its own built-in server right from your project folder, you can run the project from **any folder** on your computer. You do not need to copy files into `C:\xampp\htdocs` anymore. Any code changes you save will be live instantly!
2.  **Fault Isolation:** If the Catalog Service crashes or is stopped, the User Service still runs. Users can still register or log in, even if they can't browse books.

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

### A. Automatic Startup (Using Batch Scripts)
We provide automated helper scripts to launch both your PHP servers and the MySQL database without typing long commands.

1.  **Start the MySQL Database Server:**
    *   Open **Command Prompt** or **PowerShell** in your project folder.
    *   Run:
        ```cmd
        .\tools\start-mysql.bat
        ```
        *(This script runs: `C:\xampp\mysql\bin\mysqld.exe --defaults-file=C:\xampp\mysql\bin\my.ini --console` under the hood).*
2.  **Start all 5 Microservices:**
    *   In a new terminal window, run:
        ```cmd
        .\tools\start-microservices.bat
        ```
    *   This will open **5 separate terminal windows**, each running a PHP server on ports `8001` to `8005`. Keep these windows open while testing.

---

### B. Manual Startup (Step-by-Step commands)
If you prefer not to use the automated scripts, or if you want to troubleshoot/test a single service, you can start them manually.

#### 1. Manually Start MySQL Database Server
You can start MySQL in one of two ways:
1.  **XAMPP Control Panel:** Click the **Start** button next to **MySQL** (you do *not* need to start Apache!).
2.  **Command Line (Console):** Open a command prompt and run:
    ```cmd
    C:\xampp\mysql\bin\mysqld.exe --defaults-file=C:\xampp\mysql\bin\my.ini --console
    ```
    > [!IMPORTANT]
    > **What if you see "[ERROR] Aborting"?**
    > If you run the command above and it immediately aborts with an error, it means **MySQL is already running** (either via your XAMPP Control Panel or as a Windows system service). This is perfectly fine! It means the database is active and ready to accept connections. You can proceed directly to starting the PHP microservice servers.

#### 2. Manually Start the PHP Microservice Servers
You will need to open a separate command line window for **each** service you want to start. Navigate to your project folder first, then run:

*   **User Service (Port 8001):**
    ```cmd
    C:\xampp\php\php.exe -S localhost:8001 -t user-service
    ```
*   **Catalog Service (Port 8002):**
    ```cmd
    C:\xampp\php\php.exe -S localhost:8002 -t catalog-service
    ```
*   **Inventory Service (Port 8003):**
    ```cmd
    C:\xampp\php\php.exe -S localhost:8003 -t inventory-service
    ```
*   **Order Service (Port 8004):**
    ```cmd
    C:\xampp\php\php.exe -S localhost:8004 -t order-service
    ```
*   **Notification Service (Port 8005):**
    ```cmd
    C:\xampp\php\php.exe -S localhost:8005 -t notification-service
    ```

> [!TIP]
> If you have PHP installed globally (meaning you can type `php -v` in terminal and see the version), you can replace `C:\xampp\php\php.exe` with just `php` in any of the commands above.

---

### C. Shutting Down the Microservices
To stop the servers from listening:
-   **Method 1:** Manually close each of the separate terminal command windows.
-   **Method 2:** Open a new terminal window and run:
    ```cmd
    taskkill /F /IM php.exe
    ```
    *(Note: This instantly closes all running PHP CLI built-in server processes on your machine).*
-   To stop MySQL, press `Ctrl + C` in the MySQL console window, or click **Stop** in the XAMPP Control Panel.

---

## 5. What You Must Know About Microservice Servers (Beginner-Friendly)

If you are new to microservices, here are the most important things to keep in mind:

### 1. Terminal Windows = Server Runtimes
In a monolithic application, you had one server running in the background. In microservices:
*   **Every terminal window is a live server.** If you close the terminal running the Catalog Service (Port 8002), that service goes "offline".
*   If your app shows connection errors or API errors, check if you accidentally closed one of the terminals or if a port was already in use.

### 2. Single-Threaded Processing (One-at-a-time)
The PHP built-in server (`php -S`) is **single-threaded**. 
*   **What this means:** It can only handle **one request at a time**. 
*   If Service A makes a request to Service B, and Service B takes 5 seconds to load a database query, Service A has to wait. If multiple people try to access your local website at the same time, the server will feel slow or blocked because requests are queued up.
*   This is why we only use `php -S` for local development and testing, never for real production websites.

### 3. Databases are Shared but Decoupled
*   Even though we have 5 separate databases (`user_db`, `catalog_db`, etc.), they are all hosted on the **same MySQL database engine** (running on port `3306`).
*   This means starting the MySQL database once will make all databases available to their respective services.

### 4. No Shared Session State
*   Since the User Service runs on port `8001` and the Catalog Service runs on port `8002`, standard PHP sessions (`$_SESSION`) are not automatically shared.
*   Services must communicate through APIs (HTTP requests sending and receiving JSON data) instead of reading each other's session variables.

---

## 6. Architectural & Dev Server Limitations

> [!WARNING]
> The PHP built-in web server (`php -S`) has constraints that developers should be aware of:
> 1. **Local Staging Only:** It lacks robust security policies, logging capacity, and production-grade connection limiting. **Never run `php -S` in a production environment.**
> 2. **Static Resource Handling:** It serves static files (images, css, js) at a basic level, but it is not optimized for caching or compression headers.
