# Bookshop Management System

## 1. Project Description
Bookshop Management System is a university semester project built with PHP and MySQL on XAMPP. It uses a simplified microservices-inspired structure so beginner teams can work in parallel while keeping the project clean and professional.

## 2. Technology Stack
- PHP
- HTML
- CSS
- JavaScript
- Bootstrap
- MySQL
- XAMPP (local Apache + MySQL)

### Why We Chose This Stack
We selected this technology stack to satisfy the **mandatory microservice architecture requirement** while optimizing for **beginner-friendly development and rapid team collaboration**. Here is our rationale:

- **PHP Backend:** Chosen as the primary backend language due to its ease of setup, simplicity in HTTP request handling, and built-in support for microservice patterns. PHP allows each service to be developed and deployed independently with minimal infrastructure complexity.
- **XAMPP (Apache + MySQL):** Provides an all-in-one local development environment requiring no complex installation or configuration. This enables all team members to set up the development environment in minutes, reducing setup friction for beginners.
- **Microservice Architecture:** Each business domain (User, Catalog, Inventory, Order, Notification) is implemented as an isolated service with its own database (`user_db`, `catalog_db`, `inventory_db`, `order_db`, `notification_db`). Services communicate via HTTP APIs, following true microservice design principles.
- **HTML, CSS, JavaScript Frontend:** A lightweight frontend layer that integrates seamlessly with PHP APIs without requiring a heavy JavaScript framework, keeping the learning curve minimal.
- **Bootstrap:** Provides professional, responsive UI components out of the box, enabling rapid UI development by beginners without extensive CSS expertise.

This stack fulfills the **microservice architecture mandate** while keeping the project accessible to team members with varying levels of experience, making it ideal for a university semester project built by a diverse team.

## 3. Project Objectives
- Build a beginner-friendly enterprise-style project structure.
- Enable parallel development across team members.
- Keep each module organized with clear ownership.
- Practice clean coding, integration, and Git collaboration.

## 4. Team Members
- **Yoosuf** — Technical Lead & Integration
- **Varshi** — User Service & Documentation
- **Hijaz** — Catalog & Inventory Service
- **Rishanthan** — Order & Notification Service

## 5. Folder Structure Explanation
```text
bookshop-management-system/
├── frontend/                  # UI layer (customer/admin pages and shared assets)
│   ├── customer/
│   ├── admin/
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   └── images/
│   ├── components/
│   └── index.php
├── user-service/              # User module
├── catalog-service/           # Catalog module
├── inventory-service/         # Inventory module
├── order-service/             # Order module
├── notification-service/      # Notification module
├── shared/                    # Shared config, DB helper, reusable utilities
│   ├── config/
│   ├── database/
│   └── utils/
├── docs/                      # Setup + workflow docs
├── database/
│   ├── sql/                   # SQL scripts
│   └── diagrams/              # ER diagrams and schema images
├── .gitignore
├── README.md
└── index.php
```

## 6. Setup Instructions (Beginner Friendly)
Detailed guide: `docs/setup-guide.md`

### Step-by-step (Windows + XAMPP)
1. Install XAMPP.
2. Start **Apache** and **MySQL** from XAMPP Control Panel.
3. Clone the repo:
   ```bash
   git clone https://github.com/Yoosuf2002Nazli/Enterprise_Bookshop_Management_System.git
   ```
4. Move project into `C:\xampp\htdocs\bookshop-management-system`.
5. Open `http://localhost/phpmyadmin`.
6. Create/import databases using `database/sql/init_databases.sql, database/sql/catalog_service_schema.sql, database/sql/inventory_service_schema.sql`.
7. Open `http://localhost/bookshop-management-system/`.

## 7. Database Setup Instructions
This project uses one MySQL server (XAMPP) with separate databases per service:
- `user_db`
- `catalog_db`
- `inventory_db`
- `order_db`
- `notification_db`

Each service owns its own database schema and data. This keeps modules independent while still running on one local MySQL server.

## 8. Running the Project Locally
- Ensure Apache + MySQL are running in XAMPP.
- Open browser:
  - Home: `http://localhost/bookshop-management-system/`
  - Frontend entry: `http://localhost/bookshop-management-system/frontend/`
- Test starter API endpoints:
  - `/user-service/api/index.php`
  - `/catalog-service/api/index.php`
  - `/inventory-service/api/index.php`
  - `/order-service/api/index.php`
  - `/notification-service/api/index.php`

## 9. GitHub Workflow (Beginner Friendly)
1. Clone repository once:
   ```bash
   git clone https://github.com/Yoosuf2002Nazli/Enterprise_Bookshop_Management_System.git
   ```
2. Pull latest changes before work:
   ```bash
   git pull origin main
   ```
3. Create or update files in your module.
4. Commit clearly:
   ```bash
   git add .
   git commit -m "Add user-service login API skeleton"
   ```
5. Push changes:
   ```bash
   git push origin <your-branch-name>
   ```

## 10. Development Workflow
- Pick tasks by module ownership.
- Build feature in your service folder first.
- Add or update frontend pages if needed.
- Perform local testing with XAMPP.
- Raise integration issues quickly to technical lead.

## 11. Team Development Rules
- Pull latest code before starting work.
- Push changes daily.
- Do not modify another member's module without notice.
- Test features before pushing.
- Keep folder structure consistent.
- Report integration issues immediately.

## 12. Contribution Rules
- Keep changes small and focused.
- Use meaningful commit messages.
- Follow existing folder and naming conventions.
- Avoid committing secrets or local environment files.
- Update docs when setup or architecture changes.

## 13. API Module Overview
Each service contains:
- `api/` — HTTP endpoints (starter entry points included)
- `config/` — service-level DB and config values
- `models/` — data access/business entities
- `controllers/` — request handling logic

### Service ownership overview
- **User Service:** registration, login, profile management
- **Catalog Service:** books, categories, search
- **Inventory Service:** stock levels and updates
- **Order Service:** cart, checkout, order processing
- **Notification Service:** email/SMS notification logic (mock/local)

---
For team process details, see `docs/team-workflow.md`.
