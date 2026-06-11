# Bookshop Management System — System Architecture Summary

This document describes the design patterns, runtime models, boundaries, and communication structures of the Bookshop Management System.

---

## 1. Architectural Philosophy
This project utilizes a **simplified microservice-inspired architecture** designed for educational purposes. Rather than serving a single large monolith, the backend is partitioned into 5 independent services. 

### Why Separate Ports?
In typical XAMPP environments, different parts of an application share a single Apache web server (running on port `80` or `8080`) and communicate by directly including files (e.g. `include '../database.php'`). 
In our architecture:
* Each service operates on its own **dedicated TCP port** (`8001`–`8005`), simulating isolated hosts.
* Services communicate strictly over network boundaries using **HTTP REST APIs**.
* This enforces process isolation: if the `order-service` crashes, users can still authenticate via `user-service` and browse books via `catalog-service`.

---

## 2. System Architecture Diagram

The diagram below maps the relationships and boundaries between the UI layer, backend service APIs, and isolated database catalogs:

```mermaid
graph TD
    %% User/Client
    Client("Web Browser (User)") -->|HTTP/HTML| Frontend["Frontend Server (Port 8081)"]

    %% Frontend Server communicating with Services
    subgraph Microservice Backends
        Frontend -->|cURL / API| UserService["User Service (Port 8001)"]
        Frontend -->|cURL / API| CatalogService["Catalog Service (Port 8002)"]
        Frontend -->|cURL / API| InventoryService["Inventory Service (Port 8003)"]
        Frontend -->|cURL / API| OrderService["Order Service (Port 8004)"]
        Frontend -->|cURL / API| NotificationService["Notification Service (Port 8005)"]
    end

    %% Database Isolation Layer
    subgraph Databases (MySQL Port 3306)
        UserService -->|PDO| UserDB[("user_db")]
        CatalogService -->|PDO| CatalogDB[("catalog_db")]
        InventoryService -->|PDO| InventoryDB[("inventory_db")]
        OrderService -->|PDO| OrderDB[("order_db")]
        NotificationService -->|PDO| NotificationDB[("notification_db")]
    end

    %% Service to Service Orchestration
    OrderService -.->|cURL| InventoryService
    OrderService -.->|cURL| NotificationService
```

---

## 3. Core Architectural Boundaries

### A. User Service (Port 8001)
* **Responsibility:** Manages customer and staff accounts, credentials, and identity database queries.
* **Database:** `user_db` (stores user accounts and credentials).

### B. Catalog Service (Port 8002)
* **Responsibility:** Houses details of books (title, author, isbn, price, category, icon).
* **Database:** `catalog_db` (stores books).

### C. Inventory Service (Port 8003)
* **Responsibility:** Tracks stock quantities, restock safety limits, and replenishment pipelines.
* **Database:** `inventory_db` (tracks inventory items).

### D. Order Service (Port 8004)
* **Responsibility:** Processes checkouts, aggregates invoices, updates order states, and handles cancel requests.
* **Database:** `order_db` (tracks sales receipts).
* **Orchestration:** When an order is created, the Order Service sends backend-to-backend API calls to the Inventory Service (to subtract stock) and the Notification Service (to log an alert).

### E. Notification Service (Port 8005)
* **Responsibility:** Records system logs, low-stock warnings, and transaction receipts.
* **Database:** `notification_db` (stores alerts).

---

## 4. Frontend Integration Model (cURL Server-Side SSR)
* **Session Management:** The frontend (port `8081`) uses standard PHP session cookies (`$_SESSION`) to retain authentication state.
* **Backend-to-Backend cURL calls:** When a user interacts with the UI, the frontend server uses cURL to fetch raw JSON data from the service ports (`8001`-`8005`), parses it, and injects it into HTML templates before returning them to the user's browser. This preserves security by keeping service ports hidden from the public client.
