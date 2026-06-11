# PHP Built-in Servers Setup Guide

This guide explains how to run each microservice on a **separate port** using PHP's built-in web server. This fulfills the microservices requirement of independent, isolated services.

---

## 📋 Architecture Overview

Each service runs on its own PHP server instance with a dedicated port:

```
User Service:         http://localhost:8001
Catalog Service:      http://localhost:8002
Inventory Service:    http://localhost:8003
Order Service:        http://localhost:8004
Notification Service: http://localhost:8005
Frontend:             http://localhost:8000
```

---

## 🔧 Prerequisites

- PHP 7.4 or higher installed on your system
- MySQL server running (XAMPP or standalone)
- All databases created (user_db, catalog_db, inventory_db, order_db, notification_db)
- Git installed

---

## 📁 Project Structure

```
bookshop-management-system/
├── servers/                          # NEW: Server management scripts
│   ├── start-servers.sh             # Start all services (Linux/Mac)
│   ├── start-servers.bat            # Start all services (Windows)
│   ├── start-servers.ps1            # PowerShell script (Windows)
│   ├── stop-servers.sh              # Stop all services (Linux/Mac)
│   ├── router.php                   # Request router for frontend
│   └── README.md                    # Server management docs
├── user-service/
├── catalog-service/
├── inventory-service/
├── order-service/
├── notification-service/
├── frontend/
└── shared/
```

---

## 🚀 Quick Start Guide

### **Option 1: Windows (Batch Script)**

1. **Open Command Prompt or PowerShell** in the project root directory.

2. **Run the startup script:**
   ```batch
   cd servers
   start-servers.bat
   ```

3. **Windows Firewall Alert:** If prompted, click "Allow access" to permit PHP servers.

4. **Access the services:**
   - Frontend: http://localhost:8000
   - User Service: http://localhost:8001
   - Catalog Service: http://localhost:8002
   - Inventory Service: http://localhost:8003
   - Order Service: http://localhost:8004
   - Notification Service: http://localhost:8005

5. **To stop servers:** Close the Command Prompt windows or press `Ctrl+C` in each terminal.

---

### **Option 2: Windows (PowerShell)**

1. **Open PowerShell as Administrator**.

2. **Navigate to project:**
   ```powershell
   cd C:\path\to\Enterprise_Bookshop_Management_System\servers
   ```

3. **Run the PowerShell script:**
   ```powershell
   Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope Process
   .\start-servers.ps1
   ```

4. **Access services** (see Option 1 above).

---

### **Option 3: Linux/Mac**

1. **Open Terminal** in the project root.

2. **Make script executable:**
   ```bash
   chmod +x servers/start-servers.sh
   chmod +x servers/stop-servers.sh
   ```

3. **Start all services:**
   ```bash
   ./servers/start-servers.sh
   ```

4. **Access services:**
   - Frontend: http://localhost:8000
   - User Service: http://localhost:8001
   - Catalog Service: http://localhost:8002
   - Inventory Service: http://localhost:8003
   - Order Service: http://localhost:8004
   - Notification Service: http://localhost:8005

5. **Stop all services:**
   ```bash
   ./servers/stop-servers.sh
   ```

---

### **Option 4: Manual (Any OS)**

Start each service manually in separate terminal windows:

**Terminal 1 - Frontend:**
```bash
php -S localhost:8000 -t frontend router.php
```

**Terminal 2 - User Service:**
```bash
php -S localhost:8001 -t user-service
```

**Terminal 3 - Catalog Service:**
```bash
php -S localhost:8002 -t catalog-service
```

**Terminal 4 - Inventory Service:**
```bash
php -S localhost:8003 -t inventory-service
```

**Terminal 5 - Order Service:**
```bash
php -S localhost:8004 -t order-service
```

**Terminal 6 - Notification Service:**
```bash
php -S localhost:8005 -t notification-service
```

---

## 🔌 Service Communication

Services can now call each other independently:

```php
// Example: Catalog Service calling Inventory Service
$inventoryUrl = 'http://localhost:8003/api/inventory.php?action=check&book_id=5';
$response = file_get_contents($inventoryUrl);
$data = json_decode($response, true);
```

---

## 📊 Verifying Servers are Running

### **Check all ports:**

**Windows (Command Prompt):**
```batch
netstat -ano | findstr :800
```

**Linux/Mac (Terminal):**
```bash
lsof -i :8000
lsof -i :8001
lsof -i :8002
lsof -i :8003
lsof -i :8004
lsof -i :8005
```

### **Test API endpoints:**

```bash
# Test User Service
curl http://localhost:8001/api/index.php

# Test Catalog Service
curl http://localhost:8002/api/books.php

# Test Inventory Service
curl http://localhost:8003/api/inventory.php

# Test Order Service
curl http://localhost:8004/api/orders.php

# Test Notification Service
curl http://localhost:8005/api/notify.php
```

---

## 🔑 Database Configuration

Each service already has its own database:

```
User Service:         → user_db
Catalog Service:      → catalog_db
Inventory Service:    → inventory_db
Order Service:        → order_db
Notification Service: → notification_db
```

**Ensure MySQL is running before starting PHP servers.**

---

## ⚙️ Port Configuration

To change port numbers, edit the startup scripts:

**Windows (start-servers.bat):**
```batch
REM Change port numbers here
set FRONTEND_PORT=8000
set USER_PORT=8001
set CATALOG_PORT=8002
set INVENTORY_PORT=8003
set ORDER_PORT=8004
set NOTIFICATION_PORT=8005
```

**Linux/Mac (start-servers.sh):**
```bash
# Change port numbers here
FRONTEND_PORT=8000
USER_PORT=8001
CATALOG_PORT=8002
INVENTORY_PORT=8003
ORDER_PORT=8004
NOTIFICATION_PORT=8005
```

---

## 🐛 Troubleshooting

### **Port Already in Use**
If you get "Address already in use" error:

**Windows:**
```batch
netstat -ano | findstr :8001
taskkill /PID <PID> /F
```

**Linux/Mac:**
```bash
lsof -i :8001
kill -9 <PID>
```

### **PHP Not Found**
Ensure PHP is in your system PATH:
- **Windows:** Add PHP to environment variables
- **Linux/Mac:** Install PHP via Homebrew or package manager

### **MySQL Connection Error**
- Verify MySQL is running: `mysql -u root`
- Check database credentials in `*/config/config.php`
- Ensure all 5 databases exist

### **CORS Issues**
If frontend can't reach services, add CORS headers to each service API:

```php
// Add to each service's api/index.php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}
```

---

## 📝 Development Workflow

1. **Start all servers** using the startup script
2. **Access frontend** at http://localhost:8000
3. **Each service runs independently** on its port
4. **Services are isolated** — each can be restarted without affecting others
5. **Database per service** — data is properly partitioned

---

## 🎯 Benefits of This Setup

✅ **True Microservices**: Each service is independently deployable  
✅ **Separate Ports**: Services run on different ports (8001-8005)  
✅ **Service Isolation**: One service failure doesn't crash others  
✅ **Easy Testing**: Test each service independently  
✅ **Scalability**: Services can be scaled independently  
✅ **Team Development**: Each team member can work on one service  
✅ **Production Ready**: Foundation for Docker containerization  

---

## 🔄 Next Steps

- Implement inter-service communication
- Add API Gateway (optional)
- Set up service discovery
- Containerize with Docker for production deployment
- Add load balancing

---

## 📞 Support

For issues or questions about PHP built-in servers:
- Check PHP documentation: https://www.php.net/manual/en/features.commandline.webserver.php
- Review troubleshooting section above
- Check server logs in terminal windows
