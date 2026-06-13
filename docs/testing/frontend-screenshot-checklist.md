# Bookshop Management System — Frontend Screenshot Checklist

This checklist tracks the screenshots of the web interface (port `8081`) to be captured for submission evidence. Save the captured images into the corresponding directory inside `docs/screenshots/frontend/`.

---

## 1. Authentication Views (`docs/screenshots/frontend/auth/`)

| File Name | Page | Expected Visual Evidence | Captured? |
| :--- | :--- | :--- | :--- |
| `auth_login_blank.png` | `login.php` | Sleek login card showing email and password inputs and the sign-in button. | [ ] |
| `auth_login_simulated.png`| `login.php` | Login status panel displaying "Session Active" or "Logged in as...". | [ ] |
| `auth_register.png` | `register.php` | Complete registration form showing username, role selection, and sign-up button. | [ ] |

---

## 2. Storefront Views (`docs/screenshots/frontend/catalog/`)

| File Name | Page | Expected Visual Evidence | Captured? |
| :--- | :--- | :--- | :--- |
| `storefront_grid.png` | `books.php` | Complete grid displaying book cards with titles, authors, categories, prices, and CTA buttons. | [ ] |
| `storefront_filter.png` | `books.php` | Filtered book listing showing only items belonging to the selected category. | [ ] |
| `storefront_checkout.png` | `books.php` | Green success notification toast showing order reference after clicking "Order Now". | [ ] |

---

## 3. Inventory Dashboard Views (`docs/screenshots/frontend/inventory/`)

| File Name | Page | Expected Visual Evidence | Captured? |
| :--- | :--- | :--- | :--- |
| `inventory_table.png` | `inventory.php` | Table displaying inventory item levels, restock buttons, and warning thresholds. | [ ] |
| `inventory_low_stock.png` | `inventory.php` | Dynamic pulsing red highlights (`.low-stock-pulse`) on rows with quantity below safety levels. | [ ] |
| `inventory_restock.png` | `inventory.php` | Populated quick-restock overlay or prompt form for updating stock values. | [ ] |

---

## 4. Order Console Views (`docs/screenshots/frontend/orders/`)

| File Name | Page | Expected Visual Evidence | Captured? |
| :--- | :--- | :--- | :--- |
| `orders_list.png` | `orders.php` | Table listing order transactions, references, totals, status badges, and details links. | [ ] |
| `orders_invoice.png` | `orders.php` | Slide-out sidebar details panel displaying items, quantities, and totals. | [ ] |
| `orders_status_update.png`| `orders.php` | Toast confirmation showing the successful transition of an order status to "Shipped" or "Delivered". | [ ] |

---

## 5. Administrator Analytics Views (`docs/screenshots/frontend/admin/`)

| File Name | Page | Expected Visual Evidence | Captured? |
| :--- | :--- | :--- | :--- |
| `admin_dashboard.png` | `admin.php` | Analytics dashboard showing aggregate cards (Total Revenue, Active Warning Alerts). | [ ] |
| `admin_charts.png` | `admin.php` | Dynamic stock level and order status metrics represented in Chart.js canvases. | [ ] |
