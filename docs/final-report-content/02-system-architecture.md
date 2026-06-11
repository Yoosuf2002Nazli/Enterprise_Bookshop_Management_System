# Chapter 2: System Architecture

## 2.1 Architectural Overview
The Bookshop Management System is designed around a decoupled multi-service blueprint, separating user interface rendering from data-access business logic.

```
+-------------------------------------------------------------+
|                     Client Web Browser                      |
+-------------------------------------------------------------+
                              |
                     HTTP / HTML (Port 8081)
                              v
+-------------------------------------------------------------+
|                Frontend Server (PHP SSR)                    |
+-------------------------------------------------------------+
        |                 |                |              |
     Port 8001         Port 8002        Port 8003      Port 8004
        v                 v                v              v
+--------------+   +--------------+   +----------+   +----------+
| User Service |   |Catalog Service|  |Inventory |   |  Order   |
+--------------+   +--------------+   | Service  |   | Service  |
        |                 |           +----------+   +----------+
        v                 v                |              |
     user_db          catalog_db           v              v
                                      inventory_db     order_db
```

## 2.2 Microservices vs. Monolithic Architecture
In a monolithic application, all sub-systems share a single memory space and access a unified relational database schema. While this is simple to develop, it creates high coupling; a database lock or memory leak in one area halts the entire application.

In our system:
1. **Decoupled Runtimes:** Each of the 5 services runs its own lightweight built-in PHP web server process.
2. **Dedicated Port Boundaries:**
   * **Port 8001:** User Authentication Service (`user-service`)
   * **Port 8002:** Catalog Search & Management Service (`catalog-service`)
   * **Port 8003:** Stock & Safety Threshold Service (`inventory-service`)
   * **Port 8004:** Sales Invoicing & Checkout Service (`order-service`)
   * **Port 8005:** Alert Logging & Warnings Service (`notification-service`)
3. **Frontend Server-Side Rendering (SSR) & cURL Integration:** The frontend server (Port `8081`) acts as a client aggregator. It uses cURL to communicate with the microservices, processes JSON payloads, and outputs the resulting HTML to the user.

## 2.3 Rationale for the Port Separation Model
Port separation isolates services at the transport layer (TCP/IP). Communicating via HTTP calls ensures that service interfaces are clearly defined and standard JSON formats are used, preventing direct code level coupling.
