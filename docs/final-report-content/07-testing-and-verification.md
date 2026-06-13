# Chapter 7: Testing & Verification

## 7.1 Verification Strategy
The system's stability was verified using a three-tiered testing strategy:
1. **PHP Syntax Validation:** Ensuring clean code parsing.
2. **Automated Verification Scripting:** Testing system registration, login, navigation, and API calls.
3. **Independent Port Testing (Postman):** Validating decoupling at the transport layer by hitting backend endpoints directly in Postman.

---

## 7.2 PHP Syntax Validation
We ran syntax checks (`php -l`) recursively across all PHP files in the workspace. All files returned a clean status, confirming no compile-time syntax errors exist in the codebase:
```
No syntax errors detected in user-service/api/auth.php
No syntax errors detected in catalog-service/api/books.php
No syntax errors detected in inventory-service/api/inventory.php
No syntax errors detected in order-service/api/orders.php
No syntax errors detected in notification-service/api/notify.php
No syntax errors detected in frontend/components/config.php
...
```

---

## 7.3 Automated Integration Tests
An automated PowerShell test script (`verify-frontend.ps1`) was executed to simulate real-world user workflows on port `8081`:
* **Test 1 (Landing Page):** Visited `index.php`. Renders correctly (Status 200).
* **Test 2 (Storefront):** Visited `books.php` anonymously. Catalog loaded data from backend Catalog and Inventory services successfully.
* **Test 3 (Registration):** POSTed new staff user registration credentials to `register.php`. Account registered successfully.
* **Test 4 (Authentication):** POSTed login credentials to `login.php`. Session initialized successfully.
* **Test 5 (Staff Views):** Visited `inventory.php` authenticated. Displayed inventory tables and stock levels.
* **Test 6 (Admin Dashboard):** Visited `admin.php` authenticated. Charts and metric aggregates compiled successfully.

All integration checkpoints passed.

---

## 7.4 API Verification (Postman)
Each microservice API was tested on its respective port (`8001`–`8005`) in Postman. All 25 endpoints (covering GET list, GET by ID, POST create, PUT update, and DELETE delete/cancellation) responded with correct JSON payloads and expected HTTP status codes.
