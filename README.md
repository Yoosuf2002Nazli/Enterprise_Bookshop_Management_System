# Bookshop Management System

## 1. Project Description

The Bookshop Management System is a university project built with PHP and MySQL for the Enterprise Software Design & Architecture module. It uses a simplified microservice-inspired architecture where the frontend and backend services are separated by local service ports.

The system demonstrates:

* frontend development
* REST API implementation
* database integration
* service separation
* system integration
* final documentation and testing

## 2. Technology Stack

* PHP 8.0+
* MySQL / MariaDB
* HTML, CSS, JavaScript
* Bootstrap 5
* Postman
* GitHub
* XAMPP / PHP built-in development server

## 3. Runtime Port Mapping

| Service / App        | Port | Base URL                                  | Database              |
| -------------------- | ---: | ----------------------------------------- | --------------------- |
| Frontend UI          | 8081 | `http://localhost:8081`                   | None                  |
| User Service         | 8001 | `http://localhost:8001/api/auth.php`      | `user_db`             |
| Catalog Service      | 8002 | `http://localhost:8002/api/books.php`     | `catalog_db`          |
| Inventory Service    | 8003 | `http://localhost:8003/api/inventory.php` | `inventory_db`        |
| Order Service        | 8004 | `http://localhost:8004/api/orders.php`    | `order_db`            |
| Notification Service | 8005 | `http://localhost:8005/api/notify.php`    | `notification_db`     |
| MySQL                | 3306 | `localhost:3306`                          | All project databases |

## 4. Team Members and Roles

| Member     | Role                         | Responsibilities                                                                                        |
| ---------- | ---------------------------- | ------------------------------------------------------------------------------------------------------- |
| Yoosuf     | Technical Lead & Integration | Project structure, frontend integration, API coordination, database setup, debugging, final integration |
| Varshi     | User Service & Documentation | Login/register module, user management, documentation, API testing                                      |
| Hijaz      | Catalog & Inventory Service  | Book management, search functionality, inventory management, stock updates                              |
| Rishanthan | Order & Notification Service | Order processing, checkout workflow, notifications, order history                                       |

## 5. Project Folder Structure

```text
Enterprise_Bookshop_Management_System/
├── catalog-service/             # Catalog service, port 8002
├── database/                    # SQL scripts and database diagrams
│   ├── diagrams/
│   └── sql/
├── docs/                        # Complete project documentation
│   ├── final-report-content/    # Report-ready draft sections
│   ├── screenshots/             # Screenshot folders
│   ├── testing/                 # Test logs and checklists
│   └── *.md                     # Startup, architecture, API, and user guides
├── frontend/                    # Web frontend UI, port 8081
├── inventory-service/           # Inventory service, port 8003
├── notification-service/        # Notification service, port 8005
├── order-service/               # Order service, port 8004
├── shared/                      # Shared database and response utilities
├── tools/                       # Startup batch files
└── user-service/                # User authentication service, port 8001
```

## 6. Quick Start Instructions

### Step 1: Start MySQL

Use XAMPP Control Panel or run:

```bash
tools/start-mysql.bat
```

If the database is not already imported, import:

```text
database/sql/init_databases.sql
database/sql/phase2_schema.sql
```

### Step 2: Start Microservices

Run:

```bash
tools/start-microservices.bat
```

This starts the five backend services:

```text
User Service         → http://localhost:8001
Catalog Service      → http://localhost:8002
Inventory Service    → http://localhost:8003
Order Service        → http://localhost:8004
Notification Service → http://localhost:8005
```

### Step 3: Start Frontend

From the project root, run:

```bash
C:\xampp\php\php.exe -S localhost:8081 -t frontend
```

Open:

```text
http://localhost:8081
```

## 7. Verification

Before final submission, verify:

* MySQL is running on port `3306`
* services are running on ports `8001–8005`
* frontend is running on port `8081`
* Postman API screenshots are captured
* frontend screenshots are captured
* all PHP files pass syntax checks
* final documentation is complete

## 8. Documentation Index

Important documentation files are available in the `docs/` directory:

```text
docs/project-state.md
docs/runtime-startup-guide.md
docs/system-architecture-summary.md
docs/api-endpoint-reference.md
docs/frontend-user-guide.md
docs/database-design-guide.md
docs/testing/final-verification-checklist.md
docs/testing/postman-screenshot-checklist.md
docs/testing/frontend-screenshot-checklist.md
docs/troubleshooting-guide.md
docs/final-report-content/
```

## 9. Final Deliverables

The final project submission includes:

* working frontend system
* REST API implementation
* database integration
* service separation
* API documentation
* Postman testing screenshots
* frontend screenshots
* UML diagrams
* source code repository
* final project report

## 10. Notes

This project is an educational enterprise-inspired system. It is not intended to be a production-grade commercial deployment. The main goal is to demonstrate clear service separation, database integration, frontend-to-service communication, and complete project documentation.
