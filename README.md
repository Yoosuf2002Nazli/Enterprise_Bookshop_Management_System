# Bookshop Management System

## 1. Project Description
The Bookshop Management System is a university project built with PHP and MySQL. It uses a **simplified microservices-inspired architecture** designed for the **Enterprise Software Design & Architecture** course, enabling team members to work in parallel on isolated service modules communicating via network boundaries.

---

## 2. Technology Stack & Ports Mappings
* **PHP 8.0+**
* **MySQL / MariaDB** (standard port `3306`)
* **Bootstrap 5** & Vanilla CSS/JS
* **Postman** (for API testing)

| Service / App | Port | Base URL | Database Name |
| :--- | :--- | :--- | :--- |
| **Frontend UI (PHP SSR)** | `8081` | [http://localhost:8081](http://localhost:8081) | None (uses cURL calls) |
| **User Service** | `8001` | [http://localhost:8001/api/auth.php](http://localhost:8001/api/auth.php) | `user_db` |
| **Catalog Service** | `8002` | [http://localhost:8002/api/books.php](http://localhost:8002/api/books.php) | `catalog_db` |
| **Inventory Service** | `8003` | [http://localhost:8003/api/inventory.php](http://localhost:8003/api/inventory.php) | `inventory_db` |
| **Order Service** | `8004` | [http://localhost:8004/api/orders.php](http://localhost:8004/api/orders.php) | `order_db` |
| **Notification Service**| `8005` | [http://localhost:8005/api/notify.php](http://localhost:8005/api/notify.php) | `notification_db` |

---

## 3. Team Members & Roles
* **Yoosuf** — Technical Lead & Integration
* **Varshi** — User Service & Documentation
* **Hijaz** — Catalog & Inventory Service
* **Rishanthan** — Order & Notification Service

---

## 4. Decoupled Folder Structure
```text
Enterprise_Bookshop_Management_System/
├── catalog-service/             # Catalog service (port 8002)
├── database/                    # SQL scripts and database diagrams
│   ├── diagrams/
│   └── sql/
├── docs/                        # Complete project documentation
│   ├── final-report-content/    # Report-ready draft sections (01 to 08)
│   ├── screenshots/             # Screenshots placeholders
│   ├── testing/                 # Test logs and checklists
│   └── *.md                     # Startup, architecture, and user guides
├── frontend/                    # Web frontend UI (port 8081)
├── inventory-service/           # Inventory service (port 8003)
├── notification-service/        # Notification service (port 8005)
├── order-service/               # Order service (port 8004)
├── shared/                      # Database connection and response templates
├── tools/                       # Service and database startup batch files
└── user-service/                # User authentication service (port 8001)
```

---

## 5. Quick Start Instructions (Windows + XAMPP)

1. **Database Setup:**
   * Start MySQL from XAMPP Control Panel.
   * Import the initial creation script `database/sql/init_databases.sql`.
   * Import schemas and seed data using `database/sql/phase2_schema.sql`.

2. **Launch Databases & Microservices:**
   * Double-click `tools/start-mysql.bat` (or start MySQL in XAMPP panel).
   * Double-click `tools/start-microservices.bat` to launch the 5 backend services in separate terminal windows.

3. **Launch Frontend:**
   * Run the command below at the project root to host the UI on port `8081`:
     ```cmd
     C:\xampp\php\php.exe -S localhost:8081 -t frontend
     ```
   * Open your browser and visit: **[http://localhost:8081](http://localhost:8081)**

---

## 6. Verification & Documentation Index
All documentation files, testing evidence logs, and report content modules are housed under the **`docs/`** directory. Refer to:
* **[docs/runtime-startup-guide.md](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/docs/runtime-startup-guide.md)** for deep startup directions.
* **[docs/system-architecture-summary.md](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/docs/system-architecture-summary.md)** for structural component explanations.
* **[docs/testing/phase4-final-test-log.md](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/docs/testing/phase4-final-test-log.md)** for recent automated linter and integration testing logs.
* **[docs/final-report-content/](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/docs/final-report-content/)** for copy-pasteable university report drafts.
