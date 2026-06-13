# Chapter 5: API Documentation

This chapter contains the official REST API endpoint specification for the Bookshop Management System.

---

## 5.1 User Service (Port 8001)

### 5.1.1 Register User
* **Method:** `POST`
* **Route:** `/api/auth.php?action=register`
* **Sample Request Payload:**
  ```json
  {
    "fullname": "John Doe",
    "email": "johndoe@example.com",
    "password": "securepassword",
    "confirm_password": "securepassword",
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

### 5.1.2 Authenticate User
* **Method:** `POST`
* **Route:** `/api/auth.php?action=login`
* **Sample Request Payload:**
  ```json
  {
    "email": "johndoe@example.com",
    "password": "securepassword"
  }
  ```
* **Success Response (200 OK):**
  ```json
  {
    "status": "success",
    "message": "Login successful.",
    "email": "johndoe@example.com",
    "role": "customer"
  }
  ```

---

## 5.2 Catalog Service (Port 8002)

### 5.2.1 Retrieve All Books
* **Method:** `GET`
* **Route:** `/api/books.php`
* **Optional Query Parameters:** `category`, `search`
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
        "price": 38.99,
        "category": "Technology"
      }
    ]
  }
  ```

---

## 5.3 Inventory Service (Port 8003)

### 5.3.1 Restock Inventory
* **Method:** `POST`
* **Route:** `/api/inventory.php?action=restock`
* **Sample Request Payload:**
  ```json
  {
    "id": 1,
    "qty": 10
  }
  ```
* **Success Response (200 OK):**
  ```json
  {
    "status": "success",
    "message": "Stock successfully replenished."
  }
  ```

---

## 5.4 Order Service (Port 8004)

### 5.4.1 Create Order (Checkout)
* **Method:** `POST`
* **Route:** `/api/orders.php?action=create`
* **Sample Request Payload:**
  ```json
  {
    "customer": "John Doe",
    "email": "johndoe@example.com",
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
    "order_ref": "ORD-2026-12345"
  }
  ```

---

## 5.5 Notification Service (Port 8005)

### 5.5.1 Log Alert
* **Method:** `POST`
* **Route:** `/api/notify.php?action=log`
* **Sample Request Payload:**
  ```json
  {
    "type": "Order Placement",
    "message": "New purchase ORD-2026-12345 successfully processed.",
    "reference_id": "ORD-2026-12345"
  }
  ```
* **Success Response (201 Created):**
  ```json
  {
    "status": "success",
    "message": "Notification log recorded successfully."
  }
  ```
