# Bookshop Management System — Project State

This document provides a live ledger of the project’s progress, current completion status, architectural decisions, and remaining tasks.

## Last Updated
2026-06-11

## Phase Completion Ledger

| Phase | Status | Completion Notes |
| :--- | :--- | :--- |
| **Phase 1 — Frontend Foundation** | Completed | Mapped 6 basic layouts, setup session structure, and fallback error templates. |
| **Phase 2 — API & Database Stabilization** | Completed | Created SQL schema for 5 services; expanded Models, Controllers, and Routers to support GET/POST/PUT/DELETE. |
| **Separate Service Ports** | Completed | Setup standalone runtime. Mapped microservices to ports 8001–8005. |
| **Postman API Testing** | Completed | Verified all 25 CRUD endpoints independently in Postman on separate ports. |
| **Phase 3 — Frontend-to-Service Integration** | Completed | Refactored frontend pages to make cURL-based server-to-server requests to the port-isolated backend. |
| **Phase 4 — Hardening & Submission Prep** | **In Progress** | Writing documentation, report drafts, setting up screenshot placeholders, and doing final verification checks. |

---

## Runtime Model

The application operates as a distributed system of independent services:

* **Frontend Web Application (UI):** [http://localhost:8081](http://localhost:8081)
* **User Authentication Service:** [http://localhost:8001](http://localhost:8001)
* **Catalog Service:** [http://localhost:8002](http://localhost:8002)
* **Inventory Service:** [http://localhost:8003](http://localhost:8003)
* **Order Service:** [http://localhost:8004](http://localhost:8004)
* **Notification Service:** [http://localhost:8005](http://localhost:8005)
* **MySQL Database Engine:** `localhost:3306`

---

## Remaining Submission Tasks
- [x] Create screenshot directory structure.
- [ ] Capture final Postman execution screenshots.
- [ ] Capture final frontend walkthrough screenshots.
- [ ] Copy report drafts from `docs/final-report-content/` into the final Word/PDF university report.
- [ ] Package codebase for submission.

---

## Key Architectural Decisions
1. **PHP Sessions for Auth:** Authentication state is retained in server-side sessions on the Frontend (port 8081).
2. **Server-to-Server cURL:** The frontend performs server-side cURL HTTP requests to the individual service APIs and decodes JSON responses, maintaining a clean architectural decoupling.
3. **Soft Cancellations:** Deleting an order does not drop rows from `orders`; it transitions the record status column to `Cancelled`.
4. **Port 8081 Default:** Modified the frontend port default from `8080` to `8081` to avoid local conflicts with Oracle Database listeners.
