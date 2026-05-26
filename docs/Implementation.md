# Frontend Foundation & Microservices Integration Log

This ledger documents the implementation steps, routes, dependencies, and future integration guidelines for the Bookshop Management System frontend.

---

## 🏗️ Architectural Decisions & Error Resolutions

### 1. Relative Include Resolution
- **Problem:** Using hardcoded relative file includes like `include 'components/navbar.php';` breaks if files are run from nested routes (e.g. within subfolders like `admin/` or `customer/`).
- **Solution:** Configured all imports to resolve using the absolute file path magic constant `__DIR__`, like so:
  ```php
  include __DIR__ . '/components/navbar.php';
  ```

### 2. Elimination of Hardcoded `/frontend/` Paths
- **Problem:** Hardcoding the root route as `/frontend/...` causes the system to break when hosted inside nested subfolders on local servers (e.g., `C:\xampp\htdocs\Enterprise_Bookshop_Management_System\frontend\`).
- **Solution:** Developed a dynamic resolver utility in `components/config.php` that analyzes `$_SERVER['SCRIPT_NAME']` to extract the correct relative base URL prefix on the fly:
  ```php
  $script_name = $_SERVER['SCRIPT_NAME'];
  $frontend_pos = strpos($script_name, '/frontend/');
  $base_url = ($frontend_pos !== false) ? substr($script_name, 0, $frontend_pos + 10) : '/';
  ```
  All templates, links, scripts, and stylesheet inclusions are now reference-safe using the dynamic `<?php echo $base_url; ?>` variable.

---

## 🛠️ Step 0: Base Components & Asset Configurations

### **1. config.php** (`frontend/components/config.php`)
- **Purpose:** Centralized runtime settings, session initialization, cross-site scripting (XSS) escape functions, and dynamic path computing.
- **Dependencies:** PHP Standard Core.
- **Frontend Flow:** Automatically included at the top of every frontend script to inject path safety before page parsing.

### **2. navbar.php** (`frontend/components/navbar.php`)
- **Purpose:** Responsive header navbar featuring professional SVG branding, collapsible mobile hamburger menu, quick navigation links, and styled CTA button elements.
- **Dependencies:** Bootstrap 5, Bootstrap Icons, `$base_url`.
- **Frontend Flow:** Interlinks the primary portal pages dynamically. Highlights the active tab on the client-side using `shared.js`.

### **3. footer.php** (`frontend/components/footer.php`)
- **Purpose:** Cohesive, low-profile layout wrapper providing clean project attribution and links.
- **Dependencies:** `$base_url`, PHP Date module.

### **4. shared.css** (`frontend/assets/css/shared.css`)
- **Purpose:** Core design token definitions (luxurious academic deep blues, clean slate-gray typography, modern cards, glow outlines, pulsing warnings for low stock items, and soft input focuses).
- **Dependencies:** Google Fonts ('Inter').

### **5. shared.js** (`frontend/assets/js/shared.js`)
- **Purpose:** Dynamic client-side actions. Auto-detects the currently loaded filename to assign active state styling on navbar links. Implements auto-dismiss animations for user notifications/alerts.
- **Dependencies:** Bootstrap JS Bundle.

### **6. index.php** (`frontend/index.php`)
- **Purpose:** Homepage landing area. Presents the application purpose, academic context, and clear routing links to Customer View (Books) and Staff view (Admin).
- **Dependencies:** `config.php`, `navbar.php`, `footer.php`, `shared.css`, `shared.js`.

---

## 💻 Step 1: Implementation of Target Views & Integration Specifications

### 1. **login.php** (`frontend/login.php`)
- **Purpose:** Provides a responsive card-style login portal for student customers and bookstore administration staff. Features an active session simulator displaying logged-in credentials.
- **Routes Added:**
  - Login view: `/frontend/login.php`
  - Action (Sign Out): `/frontend/login.php?action=logout`
- **Dependencies:** `config.php`, `navbar.php`, `footer.php`, `shared.css`, `shared.js`.
- **Frontend Flow:**
  - Non-authenticated users view the sign-in input form.
  - Submitting credentials simulates validation. Successful logins create `$_SESSION['user_email']` and `$_SESSION['user_role']` arrays, triggering a soft Bootstrap notification and redirecting to the homepage.
  - Authenticated users see active session summaries and an action button to sign out (which terminates the active session).
- **Future Backend Integration Points:**
  - Form fields (`email` and `password`) will bind to the **User Service** endpoints (`../user-service/api/login.php` or `index.php`).
  - Backend response payloads (containing JWT access tokens or standard session IDs) will be parsed and stored securely inside browser session cookies.

### 2. **register.php** (`frontend/register.php`)
- **Purpose:** Allows students and staff administrators to create accounts under distinct role flags. Form validations ensure complete fields and password matches.
- **Routes Added:**
  - Register view: `/frontend/register.php`
- **Dependencies:** `config.php`, `navbar.php`, `footer.php`, `shared.css`, `shared.js`.
- **Frontend Flow:**
  - Captures Full Name, Email, Account Type (Customer vs. Staff), Password, and Password Confirmation.
  - Submitting the form simulates registration success. The page registers successful responses, flashes a success alert, and redirects the client to the login screen after 1.5 seconds.
- **Future Backend Integration Points:**
  - Registration fields will POST directly to the **User Service** endpoints (`../user-service/api/register.php`).
  - The dropdown account role selector will supply user metadata records to determine database authorization scopes (`customer` vs. `staff`).

### 3. **books.php** (`frontend/books.php`)
- **Purpose:** Customer facing storefront. Lists academic book publications with dynamic client-side authors, ISBN codes, categorization selectors, search bars, and interactive "Add to Cart" triggers.
- **Routes Added:**
  - Bookstore Storefront: `/frontend/books.php`
  - Category filter: `/frontend/books.php?category={technology|fiction|science|business}`
  - Search query: `/frontend/books.php?search={query}`
  - Add to simulated cart: `/frontend/books.php?add_cart_id={id}`
- **Dependencies:** `config.php`, `navbar.php`, `footer.php`, `shared.css`, `shared.js`.
- **Frontend Flow:**
  - Renders a search input at the top with rapid filter buttons.
  - Submitting filters computes in-memory database arrays inside standard PHP on the fly, rendering only matched book card components.
  - Add to Cart buttons dispatch get requests to simulate order checkouts, showing interactive shopping alerts.
- **Future Backend Integration Points:**
  - Book catalog arrays will be replaced with direct HTTP requests retrieving records from the **Catalog Service** endpoint (`../catalog-service/api/books.php`).
  - "Add to Cart" actions will dispatch POST actions to the **Order Service** cart manager database schemas (`../order-service/api/cart.php`).

### 4. **inventory.php** (`frontend/inventory.php`)
- **Purpose:** Staff stock dashboard. Tracks catalog amounts, low-stock trigger points, status tags, and features interactive stock replenishment quick restock triggers.
- **Routes Added:**
  - Inventory Overview: `/frontend/inventory.php`
  - Low stock warning filters: `/frontend/inventory.php?filter=low`
  - Quick Replenishment action: `/frontend/inventory.php?action=restock&id={id}&qty={qty}`
- **Dependencies:** `config.php`, `navbar.php`, `footer.php`, `shared.css`, `shared.js`, `$_SESSION['inventory_db']`.
- **Frontend Flow:**
  - Stores a stateful, session-backed log of items.
  - Applies a pulsing glow styling (`.low-stock-pulse`) to rows where current stock drops below warning thresholds, alerting administrative staff immediately.
  - Restocking items updates the session state in real-time, flashing confirmation notifications and immediately updating dashboard statistics globally.
- **Future Backend Integration Points:**
  - Replenishment actions will send PUT commands to update database column keys owned by the **Inventory Service** APIs (`../inventory-service/api/inventory.php`).

### 5. **orders.php** (`frontend/orders.php`)
- **Purpose:** Customer order ledger. Houses purchase transactions, invoices, shipping tracking status, and status adjustment drop-downs to manage shipping.
- **Routes Added:**
  - Order Ledger list: `/frontend/orders.php`
  - Select Invoice detail view: `/frontend/orders.php?view_id={id}`
  - Transition order status: `/frontend/orders.php?id={id}&action={Pending|Shipped|Delivered|Cancelled}`
- **Dependencies:** `config.php`, `navbar.php`, `footer.php`, `shared.css`, `shared.js`, `$_SESSION['orders_db']`.
- **Frontend Flow:**
  - Presents order logs with shipping status badges (Pending, Shipped, Delivered, Cancelled).
  - Clicking on Order ID references details a slide-out invoice sidebar panel reflecting purchase items, metadata summaries, and grand totals.
  - Status pipeline drop-downs allow changing active pipelines dynamically, reflecting immediately across the system.
- **Future Backend Integration Points:**
  - Order arrays will integrate with the **Order Service** query endpoints (`../order-service/api/orders.php`).
  - Order state updates will dispatch notification requests to the **Notification Service** API (`../notification-service/api/notify.php`) to email updates to student clients in real-time.

### 6. **admin.php** (`frontend/admin.php`)
- **Purpose:** Global command center for bookstore administrators. Synthesizes key performance metrics dynamically from other session tables (Revenues, Catalog Size, Warning alerts) and lists real-time low-stock alert logs.
- **Routes Added:**
  - Admin Dashboard: `/frontend/admin.php`
- **Dependencies:** `config.php`, `navbar.php`, `footer.php`, `shared.css`, `shared.js`, `$_SESSION['inventory_db']`, `$_SESSION['orders_db']`.
- **Frontend Flow:**
  - Intercepts state variables from standard sessions to compute key administrative indicators.
  - Warns administrators immediately of low-stock thresholds with blinking badge visual highlights and provides direct shortcuts to restock files.
- **Future Backend Integration Points:**
  - The dashboard will query multiple microservice APIs concurrently or call a gateway aggregator retrieving synchronized metrics summaries.
