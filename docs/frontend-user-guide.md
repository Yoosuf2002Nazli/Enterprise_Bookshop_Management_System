# Bookshop Management System — Frontend User Guide

This guide describes how to navigate and use the web-based user interface (hosted on port `8081`) and outlines which backend microservices are triggered during user interactions.

---

## 1. Landing Page (`index.php`)
* **Purpose:** The entry portal for the application. It directs the user to either the Customer storefront or the Staff management portals.
* **Integrations:** None.
* **User Workflow:**
  1. Open [http://localhost:8081/index.php](http://localhost:8081/index.php).
  2. Click **Browse Catalog** to enter as a Customer.
  3. Click **Staff Dashboard** to access the login gate for administrator panels.

---

## 2. Customer Bookstore Storefront (`books.php`)
* **Purpose:** Allows customer clients to search, filter, and order academic books.
* **Integrations:**
  * **GET** `http://localhost:8002/api/books.php` (Catalog Service - fetches book details).
  * **GET** `http://localhost:8003/api/inventory.php` (Inventory Service - fetches stock counts to check availability).
  * **POST** `http://localhost:8004/api/orders.php?action=create` (Order Service - processes checkout transactions).
* **User Workflow:**
  1. Use the **search bar** at the top or click **category filters** (e.g. Technology, Science) to find books.
  2. If an item is out of stock, the button is disabled and displays "Out of Stock".
  3. Click **Order Now** to place a simulated one-click order. A Bootstrap notification will display the generated order reference (e.g. `ORD-2026-XXXXX`).

---

## 3. Account Management Pages

### 3.1 Registration Page (`register.php`)
* **Purpose:** Registers user accounts under customer or staff role flags.
* **Integrations:**
  * **POST** `http://localhost:8001/api/auth.php?action=register` (User Service).
* **User Workflow:**
  1. Fill in Name, Email, Password, and select a Role (Customer or Staff).
  2. Click **Create Account**. On success, the UI displays a notification and redirects you to the Login screen.

### 3.2 Login Page (`login.php`)
* **Purpose:** Authentication screen for clients. Also serves as a session status monitor.
* **Integrations:**
  * **POST** `http://localhost:8001/api/auth.php?action=login` (User Service).
* **User Workflow:**
  1. Enter your registered email and password.
  2. Click **Sign In**. The system initializes the PHP session (`$_SESSION['is_logged_in'] = true`) and redirects you to the home page.
  3. If already authenticated, the page displays a **Sign Out** button, which terminates the active session.

---

## 4. Staff Management Dashboards

### 4.1 Inventory Dashboard (`inventory.php`)
* **Purpose:** Accessible only to users with the **Staff** or **Admin** role. It displays stock levels and allows rapid restocking.
* **Integrations:**
  * **GET** `http://localhost:8003/api/inventory.php` (Inventory Service - pulls all items).
  * **GET/PUT** `http://localhost:8003/api/inventory.php?action=restock` (Inventory Service - replenishes stock).
* **User Workflow:**
  1. Navigate to the Inventory page. Rows with stock counts below the safety threshold will glow with a red low-stock alert (`.low-stock-pulse`).
  2. Click **Restock** on any row to open the stock replenishment form.
  3. Enter the quantity and submit to update stock counts instantly.

### 4.2 Order Console (`orders.php`)
* **Purpose:** Lists all orders and transitions shipping statuses.
* **Integrations:**
  * **GET** `http://localhost:8004/api/orders.php` (Order Service - lists sales transactions).
  * **GET/PUT** `http://localhost:8004/api/orders.php?action=update_status` (Order Service - status transitions).
  * **DELETE** `http://localhost:8004/api/orders.php` (Order Service - order cancellation).
* **User Workflow:**
  1. Click **View Invoice** on any order reference to display a slide-out invoice sidebar details panel.
  2. Use the **Status dropdown** (Pending, Shipped, Delivered) to transition order statuses.
  3. Click the **Cancel** button on any pending transaction to execute a soft cancellation (the order status transitions to "Cancelled" in the database).

### 4.3 Administrator Analytics (`admin.php`)
* **Purpose:** Accessible only to **Admin** accounts. It showcases dynamic sales summaries, system health warnings, and low-stock alerts.
* **Integrations:**
  * **GET** `http://localhost:8004/api/orders.php` (Order Service - computes revenue aggregates).
  * **GET** `http://localhost:8003/api/inventory.php?filter=low` (Inventory Service - pulls active warnings).
  * **GET** `http://localhost:8005/api/notify.php` (Notification Service - loads system logs).
* **User Workflow:**
  1. The page calculates total revenue and active catalogs dynamically.
  2. Displays interactive charts (using Chart.js) visualizing stock levels and order statuses.
  3. Highlights recent low-stock alerts.
