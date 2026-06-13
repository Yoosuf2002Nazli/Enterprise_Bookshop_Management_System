# Bookshop Management System — Codebase Index

This document maps all key folders, files, utilities, and endpoints within the repository to explain how the components interact, what inputs they accept, and what outputs they yield.

---

## Codebase Map Table

| File/Folder | Purpose | Inputs | Outputs | Dependencies | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **`shared/database/connection_template.php`** | Provides standard PDO database connection helper. | Database credentials and names. | PDO Database Instance. | PHP PDO extension, MySQL Server. | Centralized database connection builder. |
| **`shared/utils/response.php`** | Utility helper for standardized JSON HTTP responses. | Status string, message, status code, data array. | JSON encoded string, HTTP headers. | PHP Standard Core. | Standardizes API structures. |
| **`user-service/models/UserModel.php`** | Data access layer for User data tables. | SQL query params. | Database row arrays. | `shared/database/connection_template.php`. | Encapsulates SQL user operations. |
| **`user-service/controllers/AuthController.php`** | Validates user data and manages authentication. | POST request variables (`fullname`, `email`, `password`, `role`). | Standard Response arrays. | `user-service/models/UserModel.php`. | Coordinates authentication checks. |
| **`user-service/api/auth.php`** | Main entry-point API router for the User Service (Port 8001). | JSON payload (`email`, `password`, `fullname`), URL query string actions. | JSON response message, HTTP status code. | `user-service/controllers/AuthController.php`, `shared/utils/response.php`. | Port 8001 entry point. |
| **`catalog-service/models/BookModel.php`** | Data access layer for the book catalog table. | SQL query params. | Book record arrays. | `shared/database/connection_template.php`. | Performs SELECT, INSERT, UPDATE, DELETE queries. |
| **`catalog-service/controllers/BookController.php`** | Validates and coordinates operations on book data. | User request inputs. | Formatted arrays. | `catalog-service/models/BookModel.php`. | Handlers for GET, POST, PUT, DELETE. |
| **`catalog-service/api/books.php`** | Main entry-point API router for the Catalog Service (Port 8002). | JSON payload (`title`, `author`, `isbn`, `category`, `price`), URL query string filters. | JSON list of books, success messages. | `catalog-service/controllers/BookController.php`, `shared/utils/response.php`. | Port 8002 entry point. |
| **`inventory-service/models/InventoryModel.php`**| Data access layer for the stock inventory table. | SQL query params. | Stock record arrays. | `shared/database/connection_template.php`. | Tracks quantity columns and thresholds. |
| **`inventory-service/controllers/InventoryController.php`**| Validates and handles restocking and stock updates. | User request inputs (`isbn`, `qty`). | Formatted arrays. | `inventory-service/models/InventoryModel.php`. | Handlers for stock modification verbs. |
| **`inventory-service/api/inventory.php`** | Main entry-point API router for the Inventory Service (Port 8003). | JSON payload, URL actions (`action=restock`, `id`, `qty`). | JSON stock lists, status confirmations. | `inventory-service/controllers/InventoryController.php`, `shared/utils/response.php`. | Port 8003 entry point. |
| **`order-service/models/OrderModel.php`** | Data access layer for the order sales table. | SQL query params. | Order record arrays. | `shared/database/connection_template.php`. | Saves order refs and JSON item dumps. |
| **`order-service/controllers/OrderController.php`** | Validates order constraints and tracks status. | Customer checkout fields (`customer`, `email`, `book_id`, `book_title`, `qty`, `price`). | Formatted arrays. | `order-service/models/OrderModel.php`. | Orchestrates checkouts and status changes. |
| **`order-service/api/orders.php`** | Main entry-point API router for the Order Service (Port 8004). | JSON payload, URL actions (`action=create`, `action=update_status`, `id`, `status`). | JSON receipt details, order listings. | `order-service/controllers/OrderController.php`, `shared/utils/response.php`. | Port 8004 entry point. |
| **`notification-service/models/NotificationModel.php`**| Data access layer for alert history tables. | SQL query params. | Alert log arrays. | `shared/database/connection_template.php`. | Logs system events. |
| **`notification-service/controllers/NotificationController.php`**| Validates alert types and messages. | Alert parameters. | Formatted arrays. | `notification-service/models/NotificationModel.php`. | Handles creation of alert rows. |
| **`notification-service/api/notify.php`** | Main entry-point API router for the Notification Service (Port 8005). | JSON payload (`type`, `message`, `reference_id`), URL query limits. | JSON notification lists. | `notification-service/controllers/NotificationController.php`, `shared/utils/response.php`. | Port 8005 entry point. |
| **`frontend/components/config.php`** | Dynamic URL resolver and server-to-server cURL call helper. | HTTP method, target URL, payload array. | Decoded response arrays, session setup. | PHP Standard Core, PHP cURL extension. | Decouples pages from backend ports. |
| **`frontend/components/navbar.php`** | Dynamic Bootstrap navbar layout component. | User session roles. | HTML navbar content. | PHP Standard Core, Bootstrap Icons. | Renders options based on user role. |
| **`frontend/components/footer.php`** | Cohesive HTML footer component. | None. | HTML footer content. | PHP Standard Core. | Academic copyright notice. |
| **`frontend/index.php`** | Landing portal page. | None. | HTML homepage. | `frontend/components/config.php`, navbar, footer. | Displays entry points for user/staff roles. |
| **`frontend/login.php`** | Authentication panel page. | POST request variables (`email`, `password`). | HTML user/admin panel, session initialization. | `frontend/components/config.php` (hits User Service API). | Decouples auth session. |
| **`frontend/register.php`** | Registration panel page. | POST request variables (`fullname`, `email`, `role`, `password`). | HTML register panel. | `frontend/components/config.php` (hits User Service API). | Redirects to login on success. |
| **`frontend/books.php`** | Customer storefront and book ordering page. | GET query filters (`search`, `category`). | HTML catalog grid. | `frontend/components/config.php` (hits Catalog, Inventory, Order Services). | Connects multiple APIs seamlessly. |
| **`frontend/inventory.php`** | Staff stock dashboard and restock form page. | GET actions (`action=restock`, `id`, `qty`). | HTML stock table. | `frontend/components/config.php` (hits Inventory Service API). | Restricted to Staff/Admin roles. |
| **`frontend/orders.php`** | Staff order processing and invoice page. | GET actions (`action=update_status`, `id`, `status`). | HTML order list. | `frontend/components/config.php` (hits Order Service API). | Restricted to Staff/Admin roles. |
| **`frontend/admin.php`** | Administrator business metrics analytics page. | None. | HTML aggregate metrics cards, dynamic Chart.js canvas. | `frontend/components/config.php` (hits Inventory and Order Service APIs). | Restricted to Admin role. |
| **`tools/start-microservices.bat`**| Windows batch file to start backend services. | None. | 5 PHP built-in server console windows. | PHP executable binary. | Automates local service startup. |
| **`tools/start-mysql.bat`** | Windows batch file to start XAMPP MySQL server. | None. | 1 active MySQL Server engine process. | XAMPP MySQL executable. | Automates local database startup. |
