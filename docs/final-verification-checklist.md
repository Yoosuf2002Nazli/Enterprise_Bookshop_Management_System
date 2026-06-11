# Bookshop Management System — Final Verification Checklist

This checklist acts as the final review protocol to verify that the application is stable, fully integrated, documented, and ready for university submission.

---

## 1. System Infrastructure & Port Checks
- [ ] **Database Server Check:** MySQL is running on port `3306`.
- [ ] **Database Catalogs Check:** `user_db`, `catalog_db`, `inventory_db`, `order_db`, and `notification_db` tables are initialized and loaded with seed data.
- [ ] **User Service Port Check:** Service responds on `http://localhost:8001`.
- [ ] **Catalog Service Port Check:** Service responds on `http://localhost:8002`.
- [ ] **Inventory Service Port Check:** Service responds on `http://localhost:8003`.
- [ ] **Order Service Port Check:** Service responds on `http://localhost:8004`.
- [ ] **Notification Service Port Check:** Service responds on `http://localhost:8005`.
- [ ] **Frontend Application Port Check:** Main site resolves on `http://localhost:8081`.

---

## 2. API Endpoint Sanity Checks (Postman/cURL)
- [ ] **User Service:** Register and Login endpoints return `200 OK` or `201 Created` with correct JSON.
- [ ] **Catalog Service:** Books list can be searched and filtered.
- [ ] **Inventory Service:** Restock updates quantities, and checkout reductions deduct stock correctly.
- [ ] **Order Service:** Checkouts process successfully and generate order references.
- [ ] **Notification Service:** Low-stock warnings and order alerts are logged.

---

## 3. Frontend Web Page Audits
- [ ] **Home Page (`index.php`):** Page renders correctly without PHP errors or raw source code leakage.
- [ ] **Storefront (`books.php`):** Book grid displays dynamic catalog pricing and stock levels. Out-of-stock items display disabled buttons.
- [ ] **Inventory Control (`inventory.php`):** Staff view restricts unauthorized customer roles. Displays stock counts and handles restocking.
- [ ] **Order Management (`orders.php`):** Invoices open in details sidebars, status dropdown transitions update state, and cancellations function correctly.
- [ ] **Admin Dashboard (`admin.php`):** Dashboard calculates aggregates (Revenue, Warnings) and loads dynamic charts.

---

## 4. Documentation & Code Checks
- [ ] **PHP Syntax Check:** All PHP files pass syntax checks (`php -l`).
- [ ] **Documentation Index:** All 24 required documentation guides exist inside the `docs/` folder.
- [ ] **Screenshot Directory Structure:** All required screenshot folders are created and populated.
- [ ] **Git Status:** `git status` reports that no source code files are unexpectedly modified and there are no uncommitted files.
