# Bookshop Management System — Final Submission Checklist

This checklist acts as a sanity check to verify that all university submission guidelines, code architectures, and report requirements are fully satisfied before packaging the repository.

---

## 1. Codebase Architecture & Constraints
- [x] **Service Isolation:** All five backend services run on independent TCP ports (`8001` to `8005`) and operate their own isolated database connections.
- [x] **Frontend Decoupling:** The UI (port `8081`) does not query databases directly. It communicates with services via server-to-server cURL requests.
- [x] **Session State Security:** Native PHP sessions (`$_SESSION`) and HTTPOnly cookies are utilized on the frontend (port `8081`) for authentication. No JWT is added, conforming to project boundaries.
- [x] **Zero Monolithic Dependencies:** Direct `require` calls between different services are eliminated. Services communicate strictly over HTTP.

---

## 2. Relational Database Compliance
- [x] **Database per Service:** The local MySQL server contains 5 distinct databases: `user_db`, `catalog_db`, `inventory_db`, `order_db`, and `notification_db`.
- [x] **No Cross-Database SQL Joins:** Data relation mappings (e.g. mapping orders to inventory stock) are executed at the application controller layer rather than using SQL `JOIN` statements across databases.
- [x] **SQL Schema Assets:** Init scripts (`init_databases.sql`) and database table schemas (`phase2_schema.sql`) are present in `database/sql/`.

---

## 3. Documentation Requirements
- [x] **Code Indexing:** Comprehensive codebase index maps directories and files ([docs/codebase-index.md](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/docs/codebase-index.md)).
- [x] **System Architecture Summary:** Rationale and diagrams for microservices boundaries ([docs/system-architecture-summary.md](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/docs/system-architecture-summary.md)).
- [x] **API Reference:** Explanations of API payloads, parameters, methods, and codes ([docs/api-endpoint-reference.md](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/docs/api-endpoint-reference.md)).
- [x] **UI User Guide:** Operational walkthoughs of frontend dashboards ([docs/frontend-user-guide.md](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/docs/frontend-user-guide.md)).
- [x] **Database Guide:** Explains the decoupled schema layout ([docs/database-design-guide.md](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/docs/database-design-guide.md)).
- [x] **Report Draft Sections:** Chapter drafts 1 through 8 are written and formatted under `docs/final-report-content/`.

---

## 4. Verification & Testing Evidence
- [x] **PHP Syntax Check:** All files lint checked successfully with zero compile-time syntax warnings.
- [x] **Verification Scripting:** Automated PowerShell test runner validates complete client checkout and metrics pipelines.
- [x] **Screenshot Placeholders:** Directories for Postman and frontend UI views are created.
- [x] **Handoff & Governance Ledger:** AI coding workflows and safety protocols are recorded.
