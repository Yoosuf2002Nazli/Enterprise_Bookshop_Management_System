# Chapter 1: Project Overview & Objectives

## 1.1 Project Introduction
The Bookshop Management System is a web-based software application designed as a case study for the **Enterprise Software Design & Architecture** course. The platform serves two primary groups of users: **Student Customers** (who browse textbooks, search categories, and order books) and **Bookstore Staff/Administrators** (who monitor inventory levels, restock books, manage client orders, and review sales revenue analytics).

## 1.2 Educational Rationale
Modern enterprise applications have largely transitioned from traditional monolithic structures to distributed systems. The primary educational objective of this project is to model a **simplified microservice-inspired web-service architecture**. Rather than running the system under a single server execution thread, the application is partitioned into five distinct domain-driven services, each operating on a dedicated network port. This model exposes students to key architectural concepts such as:
1. **Network Boundaries:** Communicating across TCP ports rather than direct file inclusions.
2. **Data Isolation:** Enforcing database-per-service boundaries to avoid database-level SQL JOIN dependencies.
3. **Application-Level Orchestration:** Resolving transaction pipelines (e.g. validating catalogs, subtracting stock, and recording alerts) programmatically via cURL.

## 1.3 Target Stack & Architecture
* **Frontend UI Layer:** PHP SSR (Server-Side Rendering) + HTML5 + CSS3 + Bootstrap 5 (hosted on Port `8081`).
* **Service API Layer:** 5 independent PHP services running built-in development web servers (Ports `8001`–`8005`).
* **Database Storage Layer:** MySQL database engine (Port `3306`) hosting 5 isolated database schemas (`user_db`, `catalog_db`, `inventory_db`, `order_db`, `notification_db`).
