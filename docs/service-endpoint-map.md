# Bookshop Management System — Service Endpoint Map

This document maps all backend endpoints across the 5 port-isolated services. All API endpoints communicate via JSON payloads.

---

## 1. User Service (Port 8001)

| Route / Parameter | Method | Example URL | Expected Success Response | Common Error Cases |
| :--- | :--- | :--- | :--- | :--- |
| `/api/auth.php` | `GET` | `http://localhost:8001/api/auth.php` | `{"status":"success","data":[...]}` | None (empty database returns empty list). |
| `/api/auth.php?id={id}` | `GET` | `http://localhost:8001/api/auth.php?id=1` | `{"status":"success","data":{...}}` | `404 Not Found` (User not found). |
| `/api/auth.php?action=register` | `POST` | `http://localhost:8001/api/auth.php?action=register` | `{"status":"success","message":"User registered successfully."}` | `400 Bad Request` (Email already registered or fields mismatched). |
| `/api/auth.php?action=login` | `POST` | `http://localhost:8001/api/auth.php?action=login` | `{"status":"success","message":"Login successful.",...}` | `401 Unauthorized` (Invalid email or password). |
| `/api/auth.php?id={id}` | `PUT` | `http://localhost:8001/api/auth.php?id=1` | `{"status":"success","message":"User updated successfully."}` | `400 Bad Request` (Validation errors). |
| `/api/auth.php?id={id}` | `DELETE` | `http://localhost:8001/api/auth.php?id=1` | `{"status":"success","message":"User deleted successfully."}` | `404 Not Found` (User not found). |

---

## 2. Catalog Service (Port 8002)

| Route / Parameter | Method | Example URL | Expected Success Response | Common Error Cases |
| :--- | :--- | :--- | :--- | :--- |
| `/api/books.php` | `GET` | `http://localhost:8002/api/books.php` | `{"status":"success","data":[...]}` | None (empty list returned if no matches). |
| `/api/books.php?id={id}` | `GET` | `http://localhost:8002/api/books.php?id=1` | `{"status":"success","data":{...}}` | `404 Not Found` (Book not found). |
| `/api/books.php` | `POST` | `http://localhost:8002/api/books.php` | `{"status":"success","message":"Book created successfully."}` | `400 Bad Request` (Missing fields), `409 Conflict` (ISBN exists). |
| `/api/books.php?id={id}` | `PUT` | `http://localhost:8002/api/books.php?id=1` | `{"status":"success","message":"Book updated successfully."}` | `404 Not Found`, `400 Bad Request`. |
| `/api/books.php?id={id}` | `DELETE`| `http://localhost:8002/api/books.php?id=1` | `{"status":"success","message":"Book deleted successfully."}` | `404 Not Found` (Book not found). |

---

## 3. Inventory Service (Port 8003)

| Route / Parameter | Method | Example URL | Expected Success Response | Common Error Cases |
| :--- | :--- | :--- | :--- | :--- |
| `/api/inventory.php` | `GET` | `http://localhost:8003/api/inventory.php` | `{"status":"success","data":[...]}` | None. |
| `/api/inventory.php?id={id}`| `GET` | `http://localhost:8003/api/inventory.php?id=1` | `{"status":"success","data":{...}}` | `404 Not Found` (Record not found). |
| `/api/inventory.php?action=restock&id={id}&qty={qty}` | `GET/PUT/POST` | `http://localhost:8003/api/inventory.php?action=restock&id=1&qty=5` | `{"status":"success","message":"Stock successfully replenished."}` | `400 Bad Request` (Invalid quantity). |
| `/api/inventory.php?action=reduce` | `POST` | `http://localhost:8003/api/inventory.php?action=reduce` | `{"status":"success","message":"Stock successfully reduced."}` | `400 Bad Request` (Insufficient stock or ISBN mismatch). |
| `/api/inventory.php?id={id}`| `DELETE`| `http://localhost:8003/api/inventory.php?id=1` | `{"status":"success","message":"Inventory record deleted successfully."}` | `404 Not Found` (Record not found). |

---

## 4. Order Service (Port 8004)

| Route / Parameter | Method | Example URL | Expected Success Response | Common Error Cases |
| :--- | :--- | :--- | :--- | :--- |
| `/api/orders.php` | `GET` | `http://localhost:8004/api/orders.php` | `{"status":"success","data":[...]}` | None. |
| `/api/orders.php?id={id}` | `GET` | `http://localhost:8004/api/orders.php?id=1` | `{"status":"success","data":{...}}` | `404 Not Found` (Order not found). |
| `/api/orders.php?action=create` | `POST` | `http://localhost:8004/api/orders.php?action=create` | `{"status":"success","message":"Order created successfully.","order_ref":"ORD-..."}` | `400 Bad Request` (Inventory reduction error, stock unavailable). |
| `/api/orders.php?action=update_status&id={ref}&status={status}` | `GET/PUT` | `http://localhost:8004/api/orders.php?action=update_status&id=ORD-123&status=Shipped` | `{"status":"success","message":"Order status successfully transitioned."}` | `400 Bad Request` (Invalid status flag). |
| `/api/orders.php?id={id}` | `DELETE`| `http://localhost:8004/api/orders.php?id=1` | `{"status":"success","message":"Order cancelled successfully."}` | `404 Not Found` (Order not found). |

---

## 5. Notification Service (Port 8005)

| Route / Parameter | Method | Example URL | Expected Success Response | Common Error Cases |
| :--- | :--- | :--- | :--- | :--- |
| `/api/notify.php` | `GET` | `http://localhost:8005/api/notify.php` | `{"status":"success","data":[...]}` | None. |
| `/api/notify.php?id={id}`| `GET` | `http://localhost:8005/api/notify.php?id=1` | `{"status":"success","data":{...}}` | `404 Not Found` (Log not found). |
| `/api/notify.php?action=log` | `POST` | `http://localhost:8005/api/notify.php?action=log` | `{"status":"success","message":"Notification log recorded successfully."}` | `400 Bad Request` (Missing message or type). |
| `/api/notify.php?id={id}`| `DELETE`| `http://localhost:8005/api/notify.php?id=1` | `{"status":"success","message":"Notification log deleted successfully."}` | `404 Not Found` (Log not found). |
