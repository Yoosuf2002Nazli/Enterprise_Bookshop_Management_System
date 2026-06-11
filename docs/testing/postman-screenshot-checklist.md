# Bookshop Management System — Postman Screenshot Checklist

This checklist tracks the 25 required screenshots to be captured during API testing in Postman. Save the captured images into the corresponding directory inside `docs/screenshots/postman/`.

---

## 1. User Service (`docs/screenshots/postman/user-service/`)

| File Name | HTTP Method | Target URL | Expected Status | Captured? |
| :--- | :--- | :--- | :--- | :--- |
| `1_user_get_all.png` | `GET` | `http://localhost:8001/api/auth.php` | `200 OK` | [ ] |
| `2_user_post_register.png` | `POST` | `http://localhost:8001/api/auth.php?action=register` | `201 Created` | [ ] |
| `3_user_get_by_id.png` | `GET` | `http://localhost:8001/api/auth.php?id=[ID]` | `200 OK` | [ ] |
| `4_user_put_update.png` | `PUT` | `http://localhost:8001/api/auth.php?id=[ID]` | `200 OK` | [ ] |
| `5_user_delete.png` | `DELETE` | `http://localhost:8001/api/auth.php?id=[ID]` | `200 OK` | [ ] |

---

## 2. Catalog Service (`docs/screenshots/postman/catalog-service/`)

| File Name | HTTP Method | Target URL | Expected Status | Captured? |
| :--- | :--- | :--- | :--- | :--- |
| `6_catalog_get_all.png` | `GET` | `http://localhost:8002/api/books.php` | `200 OK` | [ ] |
| `7_catalog_post_create.png` | `POST` | `http://localhost:8002/api/books.php` | `201 Created` | [ ] |
| `8_catalog_get_by_id.png` | `GET` | `http://localhost:8002/api/books.php?id=[ID]` | `200 OK` | [ ] |
| `9_catalog_put_update.png` | `PUT` | `http://localhost:8002/api/books.php?id=[ID]` | `200 OK` | [ ] |
| `10_catalog_delete.png` | `DELETE` | `http://localhost:8002/api/books.php?id=[ID]` | `200 OK` | [ ] |

---

## 3. Inventory Service (`docs/screenshots/postman/inventory-service/`)

| File Name | HTTP Method | Target URL | Expected Status | Captured? |
| :--- | :--- | :--- | :--- | :--- |
| `11_inventory_get_all.png` | `GET` | `http://localhost:8003/api/inventory.php` | `200 OK` | [ ] |
| `12_inventory_post_create.png` | `POST` | `http://localhost:8003/api/inventory.php` | `201 Created` | [ ] |
| `13_inventory_get_by_id.png` | `GET` | `http://localhost:8003/api/inventory.php?id=[ID]` | `200 OK` | [ ] |
| `14_inventory_put_update.png` | `PUT` | `http://localhost:8003/api/inventory.php?id=[ID]` | `200 OK` | [ ] |
| `15_inventory_delete.png` | `DELETE` | `http://localhost:8003/api/inventory.php?id=[ID]` | `200 OK` | [ ] |

---

## 4. Order Service (`docs/screenshots/postman/order-service/`)

| File Name | HTTP Method | Target URL | Expected Status | Captured? |
| :--- | :--- | :--- | :--- | :--- |
| `16_orders_get_all.png` | `GET` | `http://localhost:8004/api/orders.php` | `200 OK` | [ ] |
| `17_orders_post_create.png` | `POST` | `http://localhost:8004/api/orders.php?action=create` | `201 Created` | [ ] |
| `18_orders_get_by_id.png` | `GET` | `http://localhost:8004/api/orders.php?id=[ID]` | `200 OK` | [ ] |
| `19_orders_put_update.png` | `PUT` | `http://localhost:8004/api/orders.php?id=[ID]` | `200 OK` | [ ] |
| `20_orders_delete.png` | `DELETE` | `http://localhost:8004/api/orders.php?id=[ID]` | `200 OK` | [ ] |

---

## 5. Notification Service (`docs/screenshots/postman/notification-service/`)

| File Name | HTTP Method | Target URL | Expected Status | Captured? |
| :--- | :--- | :--- | :--- | :--- |
| `21_notify_get_all.png` | `GET` | `http://localhost:8005/api/notify.php` | `200 OK` | [ ] |
| `22_notify_post_create.png` | `POST` | `http://localhost:8005/api/notify.php?action=log` | `201 Created` | [ ] |
| `23_notify_get_by_id.png` | `GET` | `http://localhost:8005/api/notify.php?id=[ID]` | `200 OK` | [ ] |
| `24_notify_put_update.png` | `PUT` | `http://localhost:8005/api/notify.php?id=[ID]` | `200 OK` | [ ] |
| `25_notify_delete.png` | `DELETE` | `http://localhost:8005/api/notify.php?id=[ID]` | `200 OK` | [ ] |
