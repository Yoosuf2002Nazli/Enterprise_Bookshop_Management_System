# Bookshop Management System — API Endpoint Reference

This reference manual provides detailed HTTP payload specifications, header requirements, sample inputs/outputs, and response status codes for the microservices.

---

## Headers (Global Request Settings)
For all mutation methods (`POST`, `PUT`, `DELETE`), unless specified otherwise, you must send:
* `Content-Type: application/json`

---

## 1. User Service (Port 8001)

### 1.1 Register User
* **Method:** `POST`
* **URL:** `http://localhost:8001/api/auth.php?action=register`
* **Payload (JSON):**
  ```json
  {
    "fullname": "David Smith",
    "email": "david.smith@university.edu",
    "password": "mypassword123",
    "confirm_password": "mypassword123",
    "role": "customer"
  }
  ```
* **Success Response (201 Created):**
  ```json
  {
    "status": "success",
    "message": "User registered successfully."
  }
  ```
* **Error Response (400 Bad Request):**
  ```json
  {
    "status": "error",
    "message": "Email already registered."
  }
  ```

### 1.2 Login
* **Method:** `POST`
* **URL:** `http://localhost:8001/api/auth.php?action=login`
* **Payload (JSON):**
  ```json
  {
    "email": "david.smith@university.edu",
    "password": "mypassword123"
  }
  ```
* **Success Response (200 OK):**
  ```json
  {
    "status": "success",
    "message": "Login successful.",
    "email": "david.smith@university.edu",
    "role": "customer"
  }
  ```
* **Error Response (401 Unauthorized):**
  ```json
  {
    "status": "error",
    "message": "Invalid password."
  }
  ```

---

## 2. Catalog Service (Port 8002)

### 2.1 Get Book List
* **Method:** `GET`
* **URL:** `http://localhost:8002/api/books.php`
* **Query Params (Optional):** `category` (string), `search` (string)
* **Success Response (200 OK):**
  ```json
  {
    "status": "success",
    "data": [
      {
        "id": 1,
        "title": "Clean Code",
        "author": "Robert C. Martin",
        "isbn": "9780132350884",
        "category": "Technology",
        "price": 38.99,
        "icon": "bi-code-slash",
        "icon_color": "text-primary",
        "created_at": "2026-06-07 10:00:00"
      }
    ]
  }
  ```

### 2.2 Create Book
* **Method:** `POST`
* **URL:** `http://localhost:8002/api/books.php`
* **Payload (JSON):**
  ```json
  {
    "title": "The Pragmatic Programmer",
    "author": "Andrew Hunt",
    "isbn": "9780135957059",
    "category": "Technology",
    "price": 42.50,
    "icon": "bi-laptop",
    "icon_color": "text-secondary"
  }
  ```
* **Success Response (201 Created):**
  ```json
  {
    "status": "success",
    "message": "Book successfully created."
  }
  ```

---

## 3. Inventory Service (Port 8003)

### 3.1 Restock Item
* **Method:** `POST` (also accepts `GET` or `PUT` for compatibility)
* **URL:** `http://localhost:8003/api/inventory.php?action=restock`
* **Payload (JSON):**
  ```json
  {
    "id": 1,
    "qty": 20
  }
  ```
* **Alternative URL Query Parameters:** `?action=restock&id=1&qty=20`
* **Success Response (200 OK):**
  ```json
  {
    "status": "success",
    "message": "Stock successfully replenished."
  }
  ```

### 3.2 Reduce Stock (Checkout Trigger)
* **Method:** `POST`
* **URL:** `http://localhost:8003/api/inventory.php?action=reduce`
* **Payload (JSON):**
  ```json
  {
    "isbn": "9780132350884",
    "qty": 2
  }
  ```
* **Success Response (200 OK):**
  ```json
  {
    "status": "success",
    "message": "Stock successfully reduced."
  }
  ```

---

## 4. Order Service (Port 8004)

### 4.1 Place Order
* **Method:** `POST`
* **URL:** `http://localhost:8004/api/orders.php?action=create`
* **Payload (JSON):**
  ```json
  {
    "customer": "David Smith",
    "email": "david.smith@university.edu",
    "book_id": 1,
    "book_title": "Clean Code",
    "qty": 1,
    "price": 38.99
  }
  ```
* **Success Response (201 Created):**
  ```json
  {
    "status": "success",
    "message": "Order created successfully.",
    "order_ref": "ORD-2026-61121"
  }
  ```

### 4.2 Transition Order Status
* **Method:** `PUT` (also accepts `GET` for compatibility)
* **URL:** `http://localhost:8004/api/orders.php?action=update_status`
* **Payload (JSON):**
  ```json
  {
    "id": "ORD-2026-61121",
    "status": "Shipped"
  }
  ```
* **Alternative URL Query Parameters:** `?action=update_status&id=ORD-2026-61121&status=Shipped`
* **Success Response (200 OK):**
  ```json
  {
    "status": "success",
    "message": "Order status successfully transitioned."
  }
  ```

---

## 5. Notification Service (Port 8005)

### 5.1 Log Notification Alert
* **Method:** `POST`
* **URL:** `http://localhost:8005/api/notify.php?action=log`
* **Payload (JSON):**
  ```json
  {
    "type": "Low Stock Warning",
    "message": "Title 'Clean Code' has dropped below threshold amount.",
    "reference_id": "9780132350884"
  }
  ```
* **Success Response (201 Created):**
  ```json
  {
    "status": "success",
    "message": "Notification log recorded successfully."
  }
  ```
