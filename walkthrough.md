# Walkthrough - Phase 2 API Layer Stabilization

This document summarizes the changes, testing guide, and verification steps completed for the stabilization of the microservice API layer.

## 1. Files Inspected

* **Service Configurations**:
  * [user-service/config/config.php](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/user-service/config/config.php)
  * [catalog-service/config/config.php](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/catalog-service/config/config.php)
  * [inventory-service/config/config.php](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/inventory-service/config/config.php)
  * [order-service/config/config.php](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/order-service/config/config.php)
  * [notification-service/config/config.php](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/notification-service/config/config.php)
* **API Routing Entry Points**:
  * [user-service/api/auth.php](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/user-service/api/auth.php)
  * [catalog-service/api/books.php](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/catalog-service/api/books.php)
  * [inventory-service/api/inventory.php](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/inventory-service/api/inventory.php)
  * [order-service/api/orders.php](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/order-service/api/orders.php)
  * [notification-service/api/notify.php](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/notification-service/api/notify.php)
* **Shared Utilities**:
  * [shared/database/connection_template.php](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/shared/database/connection_template.php)
  * [shared/utils/response.php](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/shared/utils/response.php)
* **Access Control configuration**:
  * `frontend/.htaccess`

---

## 2. Problems Found

1. **Missing CRUD actions**: Most services only supported their minimal needed operations (e.g. catalog service only supported GET filter/search, user service only supported POST register/login/logout). They lacked general GET all, GET one by ID, POST create, PUT update, and DELETE actions required for comprehensive API testing.
2. **GET-routed Updates**: Actions that mutate state (e.g. inventory restocking and order status updates) were routed under HTTP GET request methods instead of PUT/POST methods.
3. **Missing Request Body Parsers**: Microservice routers did not parse raw JSON input payloads (`php://input`), restricting request parameters strictly to `$_POST` (urlencoded form data) or URL queries.

---

## 3. Files Changed

We extended the Models, Controllers, and API files for all five services without altering existing logic, database tables, or seed data:

* **user-service**:
  * [UserModel.php](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/user-service/models/UserModel.php) (Added `getAllUsers`, `getUserById`, `updateUser`, `deleteUser`)
  * [AuthController.php](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/user-service/controllers/AuthController.php) (Added `handleGetUsers`, `handleGetUserById`, `handleUpdateUser`, `handleDeleteUser`)
  * [auth.php](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/user-service/api/auth.php) (Updated routing to merge JSON bodies and support GET/PUT/DELETE request methods)
* **catalog-service**:
  * [BookModel.php](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/catalog-service/models/BookModel.php) (Added `createBook`, `updateBook`, `deleteBook`)
  * [BookController.php](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/catalog-service/controllers/BookController.php) (Added `handleGetBookById`, `handleCreateBook`, `handleUpdateBook`, `handleDeleteBook`)
  * [books.php](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/catalog-service/api/books.php) (Updated routing to support GET single, POST create, PUT update, and DELETE methods)
* **inventory-service**:
  * [InventoryModel.php](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/inventory-service/models/InventoryModel.php) (Added `getById`, `createInventory`, `updateInventory`, `deleteInventory`)
  * [InventoryController.php](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/inventory-service/controllers/InventoryController.php) (Added `handleGetInventoryById`, `handleCreateInventory`, `handleUpdateInventory`, `handleDeleteInventory`)
  * [inventory.php](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/inventory-service/api/inventory.php) (Extended routing to support GET single, POST create, PUT update, and DELETE methods while keeping compatibility with existing restock and reduce actions)
* **order-service**:
  * [OrderModel.php](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/order-service/models/OrderModel.php) (Added `getOrderById`, `updateOrder`, and soft cancellation-based `deleteOrder`)
  * [OrderController.php](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/order-service/controllers/OrderController.php) (Added `handleGetOrderById`, `handleUpdateOrder`, `handleDeleteOrder`)
  * [orders.php](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/order-service/api/orders.php) (Extended routing to support GET single, POST create, PUT update, and DELETE cancel methods)
* **notification-service**:
  * [NotificationModel.php](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/notification-service/models/NotificationModel.php) (Added `getLogById`, `updateLog`, `deleteLog`)
  * [NotificationController.php](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/notification-service/controllers/NotificationController.php) (Added `handleGetLogById`, `handleUpdateLog`, `handleDeleteLog`)
  * [notify.php](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/notification-service/api/notify.php) (Extended routing to support GET single, POST create, PUT update, and DELETE methods)

---

## 4. SQL Patches

**No SQL patch was needed.**
* Schema and databases were fully inspected using native MySQL commands and confirmed to be completely intact, matching the exact definitions from `phase2_schema.sql`.

---

## 5. Postman Request List & Testing Guide

Use `{{base_url}} = http://localhost/Enterprise_Bookshop_Management_System`. All request bodies should be set as raw JSON unless noted.

### 5.1 User Service (`user-service`)
1. **GET All Users**
   * **Method**: `GET`
   * **Endpoint**: `{{base_url}}/user-service/api/auth.php`
   * **Headers**: None
   * **Expected Status**: `200 OK`
   * **Expected Response**: `{"status":"success","data":[...]}`
   * **Screenshot File**: `1_user_get_all.png`

2. **POST Register User**
   * **Method**: `POST`
   * **Endpoint**: `{{base_url}}/user-service/api/auth.php?action=register`
   * **Headers**: `Content-Type: application/json`
   * **Body Type**: JSON (or `x-www-form-urlencoded`)
   * **Sample Body**:
     ```json
     {
       "fullname": "API Test User",
       "email": "apitest@university.edu",
       "password": "password123",
       "confirm_password": "password123",
       "role": "customer"
     }
     ```
   * **Expected Status**: `201 Created`
   * **Expected Response**: `{"status":"success","message":"User registered successfully."}`
   * **Screenshot File**: `2_user_post_register.png`

3. **GET User By ID**
   * **Method**: `GET`
   * **Endpoint**: `{{base_url}}/user-service/api/auth.php?id=4` (Replace with the created user's ID)
   * **Headers**: None
   * **Expected Status**: `200 OK`
   * **Expected Response**: `{"status":"success","data":{"id":4,"fullname":"API Test User",...}}`
   * **Screenshot File**: `3_user_get_by_id.png`

4. **PUT Update User**
   * **Method**: `PUT`
   * **Endpoint**: `{{base_url}}/user-service/api/auth.php?id=4` (Replace with the user's ID)
   * **Headers**: `Content-Type: application/json`
   * **Sample Body**:
     ```json
     {
       "fullname": "API Test User Updated",
       "email": "apitest@university.edu",
       "role": "staff"
     }
     ```
   * **Expected Status**: `200 OK`
   * **Expected Response**: `{"status":"success","message":"User updated successfully.","data":{...}}`
   * **Screenshot File**: `4_user_put_update.png`

5. **DELETE User**
   * **Method**: `DELETE`
   * **Endpoint**: `{{base_url}}/user-service/api/auth.php?id=4` (Replace with the user's ID)
   * **Headers**: None
   * **Expected Status**: `200 OK`
   * **Expected Response**: `{"status":"success","message":"User deleted successfully."}`
   * **Screenshot File**: `5_user_delete.png`

### 5.2 Catalog Service (`catalog-service`)
1. **GET All Books**
   * **Method**: `GET`
   * **Endpoint**: `{{base_url}}/catalog-service/api/books.php`
   * **Headers**: None
   * **Expected Status**: `200 OK`
   * **Expected Response**: `{"status":"success","data":[...]}`
   * **Screenshot File**: `6_catalog_get_all.png`

2. **POST Create Book**
   * **Method**: `POST`
   * **Endpoint**: `{{base_url}}/catalog-service/api/books.php`
   * **Headers**: `Content-Type: application/json`
   * **Sample Body**:
     ```json
     {
       "title": "API Test Book",
       "author": "Test Author",
       "isbn": "978-0000000001",
       "category": "Technology",
       "price": 45.99,
       "icon": "bi-code",
       "icon_color": "text-success"
     }
     ```
   * **Expected Status**: `201 Created`
   * **Expected Response**: `{"status":"success","message":"Book created successfully."}`
   * **Screenshot File**: `7_catalog_post_create.png`

3. **GET Book By ID**
   * **Method**: `GET`
   * **Endpoint**: `{{base_url}}/catalog-service/api/books.php?id=7` (Replace with the created book's ID)
   * **Headers**: None
   * **Expected Status**: `200 OK`
   * **Expected Response**: `{"status":"success","data":{"id":7,"title":"API Test Book",...}}`
   * **Screenshot File**: `8_catalog_get_by_id.png`

4. **PUT Update Book**
   * **Method**: `PUT`
   * **Endpoint**: `{{base_url}}/catalog-service/api/books.php?id=7` (Replace with the book's ID)
   * **Headers**: `Content-Type: application/json`
   * **Sample Body**:
     ```json
     {
       "title": "API Test Book Updated",
       "author": "Test Author Updated",
       "isbn": "978-0000000001",
       "category": "Technology",
       "price": 49.99,
       "icon": "bi-code-square",
       "icon_color": "text-danger"
     }
     ```
   * **Expected Status**: `200 OK`
   * **Expected Response**: `{"status":"success","message":"Book updated successfully."}`
   * **Screenshot File**: `9_catalog_put_update.png`

5. **DELETE Book**
   * **Method**: `DELETE`
   * **Endpoint**: `{{base_url}}/catalog-service/api/books.php?id=7` (Replace with the book's ID)
   * **Headers**: None
   * **Expected Status**: `200 OK`
   * **Expected Response**: `{"status":"success","message":"Book deleted successfully."}`
   * **Screenshot File**: `10_catalog_delete.png`

### 5.3 Inventory Service (`inventory-service`)
1. **GET All Inventory**
   * **Method**: `GET`
   * **Endpoint**: `{{base_url}}/inventory-service/api/inventory.php`
   * **Headers**: None
   * **Expected Status**: `200 OK`
   * **Expected Response**: `{"status":"success","data":[...]}`
   * **Screenshot File**: `11_inventory_get_all.png`

2. **POST Create Inventory**
   * **Method**: `POST`
   * **Endpoint**: `{{base_url}}/inventory-service/api/inventory.php`
   * **Headers**: `Content-Type: application/json`
   * **Sample Body**:
     ```json
     {
       "book_id": 999,
       "isbn": "978-0000000002",
       "title": "API Test Inventory Item",
       "category": "Fiction",
       "stock": 10,
       "threshold": 3
     }
     ```
   * **Expected Status**: `201 Created`
   * **Expected Response**: `{"status":"success","message":"Inventory record created successfully."}`
   * **Screenshot File**: `12_inventory_post_create.png`

3. **GET Inventory By ID**
   * **Method**: `GET`
   * **Endpoint**: `{{base_url}}/inventory-service/api/inventory.php?id=7` (Replace with the created inventory's ID)
   * **Headers**: None
   * **Expected Status**: `200 OK`
   * **Expected Response**: `{"status":"success","data":{"id":7,...}}`
   * **Screenshot File**: `13_inventory_get_by_id.png`

4. **PUT Update Inventory**
   * **Method**: `PUT`
   * **Endpoint**: `{{base_url}}/inventory-service/api/inventory.php?id=7` (Replace with the inventory's ID)
   * **Headers**: `Content-Type: application/json`
   * **Sample Body**:
     ```json
     {
       "book_id": 999,
       "isbn": "978-0000000002",
       "title": "API Test Inventory Item Updated",
       "category": "Fiction",
       "stock": 15,
       "threshold": 5
     }
     ```
   * **Expected Status**: `200 OK`
   * **Expected Response**: `{"status":"success","message":"Inventory record updated successfully."}`
   * **Screenshot File**: `14_inventory_put_update.png`

5. **DELETE Inventory**
   * **Method**: `DELETE`
   * **Endpoint**: `{{base_url}}/inventory-service/api/inventory.php?id=7` (Replace with the inventory's ID)
   * **Headers**: None
   * **Expected Status**: `200 OK`
   * **Expected Response**: `{"status":"success","message":"Inventory record deleted successfully."}`
   * **Screenshot File**: `15_inventory_delete.png`

### 5.4 Order Service (`order-service`)
1. **GET All Orders**
   * **Method**: `GET`
   * **Endpoint**: `{{base_url}}/order-service/api/orders.php`
   * **Headers**: None
   * **Expected Status**: `200 OK`
   * **Expected Response**: `{"status":"success","data":[...]}`
   * **Screenshot File**: `16_orders_get_all.png`

2. **POST Create Order**
   * **Method**: `POST`
   * **Endpoint**: `{{base_url}}/order-service/api/orders.php`
   * **Headers**: `Content-Type: application/json`
   * **Sample Body**:
     ```json
     {
       "customer": "API Test Order Customer",
       "email": "apitestorder@university.edu",
       "book_id": 3,
       "book_title": "Dune",
       "qty": 1,
       "price": 14.99
     }
     ```
   * **Expected Status**: `201 Created`
   * **Expected Response**: `{"status":"success","message":"Order created successfully.","order_ref":"ORD-..."}`
   * **Screenshot File**: `17_orders_post_create.png`

3. **GET Order By ID**
   * **Method**: `GET`
   * **Endpoint**: `{{base_url}}/order-service/api/orders.php?id=4` (Replace with the created order's ID)
   * **Headers**: None
   * **Expected Status**: `200 OK`
   * **Expected Response**: `{"status":"success","data":{"id":4,"order_ref":"ORD-...",...}}`
   * **Screenshot File**: `18_orders_get_by_id.png`

4. **PUT Update Order**
   * **Method**: `PUT`
   * **Endpoint**: `{{base_url}}/order-service/api/orders.php?id=4` (Replace with the order's ID)
   * **Headers**: `Content-Type: application/json`
   * **Sample Body**:
     ```json
     {
       "customer": "API Test Order Customer Updated",
       "email": "apitestorder@university.edu",
       "total": 14.99,
       "status": "Shipped"
     }
     ```
   * **Expected Status**: `200 OK`
   * **Expected Response**: `{"status":"success","message":"Order updated successfully."}`
   * **Screenshot File**: `19_orders_put_update.png`

5. **DELETE Order (Soft Cancellation)**
   * **Method**: `DELETE`
   * **Endpoint**: `{{base_url}}/order-service/api/orders.php?id=4` (Replace with the order's ID)
   * **Headers**: None
   * **Expected Status**: `200 OK`
   * **Expected Response**: `{"status":"success","message":"Order cancelled successfully."}`
   * **Screenshot File**: `20_orders_delete.png`

### 5.5 Notification Service (`notification-service`)
1. **GET All Notifications**
   * **Method**: `GET`
   * **Endpoint**: `{{base_url}}/notification-service/api/notify.php`
   * **Headers**: None
   * **Expected Status**: `200 OK`
   * **Expected Response**: `{"status":"success","data":[...]}`
   * **Screenshot File**: `21_notify_get_all.png`

2. **POST Create Notification**
   * **Method**: `POST`
   * **Endpoint**: `{{base_url}}/notification-service/api/notify.php`
   * **Headers**: `Content-Type: application/json`
   * **Sample Body**:
     ```json
     {
       "type": "API Test Notification",
       "message": "This is a test notification message from API testing.",
       "reference_id": "REF-TEST-123"
     }
     ```
   * **Expected Status**: `201 Created`
   * **Expected Response**: `{"status":"success","message":"Notification log recorded successfully."}`
   * **Screenshot File**: `22_notify_post_create.png`

3. **GET Notification By ID**
   * **Method**: `GET`
   * **Endpoint**: `{{base_url}}/notification-service/api/notify.php?id=1` (Replace with the created notification ID)
   * **Headers**: None
   * **Expected Status**: `200 OK`
   * **Expected Response**: `{"status":"success","data":{"id":1,"type":"API Test Notification",...}}`
   * **Screenshot File**: `23_notify_get_by_id.png`

4. **PUT Update Notification**
   * **Method**: `PUT`
   * **Endpoint**: `{{base_url}}/notification-service/api/notify.php?id=1` (Replace with the notification ID)
   * **Headers**: `Content-Type: application/json`
   * **Sample Body**:
     ```json
     {
       "type": "API Test Notification Updated",
       "message": "Updated test notification message.",
       "reference_id": "REF-TEST-123-UPDATED"
     }
     ```
   * **Expected Status**: `200 OK`
   * **Expected Response**: `{"status":"success","message":"Notification log updated successfully."}`
   * **Screenshot File**: `24_notify_put_update.png`

5. **DELETE Notification**
   * **Method**: `DELETE`
   * **Endpoint**: `{{base_url}}/notification-service/api/notify.php?id=1` (Replace with the notification ID)
   * **Headers**: None
   * **Expected Status**: `200 OK`
   * **Expected Response**: `{"status":"success","message":"Notification log deleted successfully."}`
   * **Screenshot File**: `25_notify_delete.png`

---

## 6. Exact Screenshot Checklist

Capture and document screenshots in Postman with the following filenames:
1. `1_user_get_all.png` (GET all users showing status 200 and data)
2. `2_user_post_register.png` (POST register returning 201 success)
3. `3_user_get_by_id.png` (GET single user details by ID returning 200)
4. `4_user_put_update.png` (PUT update user details returning 200)
5. `5_user_delete.png` (DELETE user returning 200 success)
6. `6_catalog_get_all.png` (GET all books listing returning 200)
7. `7_catalog_post_create.png` (POST book returning 210 success)
8. `8_catalog_get_by_id.png` (GET book details by ID returning 200)
9. `9_catalog_put_update.png` (PUT update book details returning 200)
10. `10_catalog_delete.png` (DELETE book returning 200 success)
11. `11_inventory_get_all.png` (GET all inventory records returning 200)
12. `12_inventory_post_create.png` (POST inventory record returning 201)
13. `13_inventory_get_by_id.png` (GET inventory record by ID returning 200)
14. `14_inventory_put_update.png` (PUT update inventory record returning 200)
15. `15_inventory_delete.png` (DELETE inventory record returning 200 success)
16. `16_orders_get_all.png` (GET all orders and line items returning 200)
17. `17_orders_post_create.png` (POST create order returning 201)
18. `18_orders_get_by_id.png` (GET order by ID returning 200)
19. `19_orders_put_update.png` (PUT update order details returning 200)
20. `20_orders_delete.png` (DELETE cancel order soft cancellation status returning 200)
21. `21_notify_get_all.png` (GET all notification logs returning 200)
22. `22_notify_post_create.png` (POST log notification returning 201)
23. `23_notify_get_by_id.png` (GET notification log by ID returning 200)
24. `24_notify_put_update.png` (PUT update notification log returning 200)
25. `25_notify_delete.png` (DELETE notification log returning 200)

---

## 7. Manual Verification Instructions

### 7.1 Running Automated Linting Check
Verify syntax consistency by running:
```powershell
C:\xampp\php\php.exe -f C:\Users\ASUS\.gemini\antigravity-ide\brain\749da90b-bffc-44ff-8bca-b9a0ceabbf58\scratch\lint_all.php
```

### 7.2 Running CRUD API Verification Script
Run the automated test runner in PowerShell:
```powershell
C:\xampp\php\php.exe -f C:\Users\ASUS\.gemini\antigravity-ide\brain\749da90b-bffc-44ff-8bca-b9a0ceabbf58\scratch\test_crud.php
```

### 7.3 Postman Smoke Test
1. Set up a Postman Environment with `base_url` set to `http://localhost/Enterprise_Bookshop_Management_System`.
2. Create and execute requests matching the exact guide above.
3. Verify that each response is in valid JSON, content headers match `application/json`, and status codes align.
4. Verify that orders deleted via the DELETE verb have their database `status` changed to `Cancelled` rather than being removed from the table.

---

## 8. Linter Check Results

```
PASSED: user-service/api/auth.php
PASSED: user-service/models/UserModel.php
PASSED: user-service/controllers/AuthController.php
PASSED: catalog-service/api/books.php
PASSED: catalog-service/models/BookModel.php
PASSED: catalog-service/controllers/BookController.php
PASSED: inventory-service/api/inventory.php
PASSED: inventory-service/models/InventoryModel.php
PASSED: inventory-service/controllers/InventoryController.php
PASSED: order-service/api/orders.php
PASSED: order-service/models/OrderModel.php
PASSED: order-service/controllers/OrderController.php
PASSED: notification-service/api/notify.php
PASSED: notification-service/models/NotificationModel.php
PASSED: notification-service/controllers/NotificationController.php

ALL FILES LINTED SUCCESSFULLY!
```
