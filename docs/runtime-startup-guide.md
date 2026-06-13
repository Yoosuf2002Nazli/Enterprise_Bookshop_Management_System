# Bookshop Management System — Runtime Startup Guide

This document describes how to start, monitor, and shut down the Bookshop Management System on your local development machine. 

---

## Prerequisites
1. **PHP 8.0+** (either installed globally or via XAMPP).
2. **MySQL / MariaDB** (standard port `3306`, usually through XAMPP).
3. **PowerShell** or **Command Prompt** (Windows OS).

---

## 1. Starting the MySQL Database
You can initialize the database engine in one of two ways:

### Method A: Using the Automated Script (Recommended)
1. Navigate to the root directory of your project.
2. Open a command prompt and run:
   ```cmd
   .\tools\start-mysql.bat
   ```
   This script will launch the database server using XAMPP's defaults (`C:\xampp\mysql\bin\mysqld.exe`).

### Method B: Using XAMPP Control Panel
1. Open the **XAMPP Control Panel**.
2. Click the **Start** button next to **MySQL** (you do *not* need to start Apache!).

*Note: If MySQL fails to start and warns that port 3306 is already in use, it means a local MySQL service is already active. This is fine; you can skip this step and proceed.*

---

## 2. Starting the Backend Microservices
We run 5 separate services on ports 8001–8005.

1. Navigate to the root directory of the project.
2. Open a command prompt and run:
   ```cmd
   .\tools\start-microservices.bat
   ```
3. This will launch **5 separate command prompt windows**, each running a local PHP server instance:
   * **User Service:** [http://localhost:8001](http://localhost:8001)
   * **Catalog Service:** [http://localhost:8002](http://localhost:8002)
   * **Inventory Service:** [http://localhost:8003](http://localhost:8003)
   * **Order Service:** [http://localhost:8004](http://localhost:8004)
   * **Notification Service:** [http://localhost:8005](http://localhost:8005)

**Important:** Keep these windows open while utilizing the application.

---

## 3. Starting the Frontend UI Server
We host the frontend on port **8081** to avoid conflicts with Oracle Database listener instances that frequently lock port 8080.

1. Open a new terminal/command prompt window at the project root.
2. Execute the following command:
   ```cmd
   C:\xampp\php\php.exe -S localhost:8081 -t frontend
   ```
3. Open your web browser and navigate to:
   * **[http://localhost:8081](http://localhost:8081)**

---

## 4. Shutting Down the Application

### Safe Shutdown
* Click close (`[X]`) on each of the 5 terminal windows running the microservices, and close the terminal running the frontend.
* Stop MySQL inside XAMPP Control Panel.

### Force Recovery Command (If Port Locks Occur)
If a terminal window was closed but the PHP process remains active in the background (holding the port locked), run the following command in command prompt or PowerShell to terminate all PHP servers instantly:
```cmd
taskkill /F /IM php.exe
```

---

## 5. Troubleshooting Port Conflicts
If a port collision occurs (e.g. error message `Address already in use`):
1. Run this command to identify which process PID is locking a port (e.g. port `8001`):
   ```cmd
   netstat -aon | findstr :8001
   ```
2. Kill the conflicting process using its PID (replace `[PID]` with the actual number from the netstat output):
   ```cmd
   taskkill /F /PID [PID]
   ```
3. Or change the port within the `tools/start-microservices.bat` script and update corresponding URL definitions inside [frontend/components/config.php](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/frontend/components/config.php).
