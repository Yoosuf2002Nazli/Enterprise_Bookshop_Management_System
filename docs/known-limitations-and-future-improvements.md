# Bookshop Management System — Limitations & Future Improvements

This document outlines the accepted architectural design tradeoffs, scope boundaries, and future recommendations for transitioning this project from a local educational prototype to a production-grade system.

---

## 1. Accepted Architectural Tradeoffs

### 1.1 Development Server Engine (`php -S`)
* **Limitation:** The backend services and frontend utilize the built-in PHP development server engine. This server is **single-threaded**, meaning requests are queued and processed one at a time.
* **Tradeoff:** This is ideal for lightweight local development and has no configuration overhead. However, it cannot handle high concurrent traffic or production request volumes.

### 1.2 Session-Based Authorization vs. JWT
* **Limitation:** Authentication is managed via PHP sessions (`$_SESSION`) stored on the frontend web server. Service endpoints do not validate user identity tokens directly.
* **Tradeoff:** Sessions are easier to implement and test. In a production microservice architecture, stateless JSON Web Tokens (JWT) would be preferred to let backends validate identity cryptographically.

### 1.3 Application-Layer Relational Integrity
* **Limitation:** Databases are completely decoupled, and there are no foreign keys or database-level cascading operations across databases.
* **Tradeoff:** Enforces microservice data boundaries but increases the risk of data inconsistency if an API call fails mid-transaction.

---

## 2. Known Limitations & Technical Debt

### 2.1 Lack of Database Transaction Rollbacks (Sagas)
* **Symptom:** During order placement, if catalog database verification succeeds, stock is deducted from `inventory_db`, but creating the invoice in `order_db` subsequently fails, the inventory stock is not automatically rolled back.
* **Impact:** Inventory levels might occasionally become desynchronized.

### 2.2 Direct Database Connection Credentials
* **Symptom:** Connection parameters are defined directly in shared scripts rather than environment files (`.env`).
* **Impact:** Low security compliance.

### 2.3 Absence of Real Payment Gateways & Email Servers
* **Symptom:** Purchases are immediately processed as successful checkout without card checks. Notifications are written to SQL logs instead of sent via SMTP.
* **Impact:** Reduced functionality, acceptable for the educational scope of the course.

---

## 3. Future Improvements Roadmap

1. **State Orchestration (Sagas):** Implement transaction rollback logic to ensure that if any step in the checkout pipeline fails, all previous database updates are rolled back automatically.
2. **Stateless JWT Authorization:** Migrate user authentication from server-side PHP sessions to JWT tokens passed in the request Authorization headers.
3. **Containerization (Docker):** Standardize deployment and orchestration across all services by containerizing the front and backends.
4. **API Gateway Aggregation:** Introduce an API Gateway (e.g. Kong, NGINX) to expose a single entry point for clients, routing queries to the correct service ports automatically.
