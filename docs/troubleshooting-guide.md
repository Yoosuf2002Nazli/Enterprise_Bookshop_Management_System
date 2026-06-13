# Bookshop Management System — Troubleshooting Guide

This guide compiles common runtime errors, configuration issues, and port conflicts encountered during local deployment, along with step-by-step instructions for resolution.

---

## 1. Database Connection & Access Errors

### 1.1 Error: `SQLSTATE[HY000] [2002] Connection refused`
* **Symptom:** API endpoints return 500 error logs or pages report that the database connection has failed.
* **Cause:** The MySQL database server is not running on port `3306`.
* **Resolution:**
  1. Open XAMPP Control Panel and verify that MySQL is started (green indicator).
  2. If running via command prompt, run:
     ```cmd
     .\tools\start-mysql.bat
     ```
  3. Verify port `3306` is open:
     ```cmd
     netstat -ano | findstr :3306
     ```

### 1.2 Error: `Access denied for user 'root'@'localhost'`
* **Symptom:** Connection aborted due to database password failures.
* **Cause:** The MySQL server has a root password set, but the project configurations assume no password.
* **Resolution:**
  * Open [shared/database/connection_template.php](file:///c:/Users/ASUS/workspace/Projects/Enterprise_Bookshop_Management_System/shared/database/connection_template.php) and update the `password` argument from `""` to match your local MySQL configuration.

---

## 2. Port & Network Boundaries Errors

### 2.1 Error: `Address already in use` or Port Locking
* **Symptom:** Terminal command prompt windows immediately close after running `start-microservices.bat`.
* **Cause:** Another service or process is already listening on one of the designated ports (`8001` to `8005`).
* **Resolution:**
  1. Find the process ID (PID) using the port (e.g. port `8001`):
     ```cmd
     netstat -aon | findstr :8001
     ```
  2. Terminate the process (replace `[PID]` with the actual number from step 1):
     ```cmd
     taskkill /F /PID [PID]
     ```
  3. Alternatively, close all active PHP background instances instantly:
     ```cmd
     taskkill /F /IM php.exe
     ```

### 2.2 Local Host Resolution (`localhost` vs `127.0.0.1`)
* **Symptom:** Services take up to 1 second to respond or connections fail.
* **Cause:** Certain local Windows configurations resolve `localhost` via IPv6 (`::1`), which causes delays or connection refusals if MySQL/PHP is only listening on IPv4.
* **Resolution:**
  * Change the connection parameters inside `shared/database/connection_template.php` and service configuration files from `localhost` to `127.0.0.1`.

---

## 3. PHP Engine & Rendering Failures

### 3.1 Symptom: Raw PHP Source Code is displayed in the browser
* **Symptom:** Opening the page displays the raw text code of index.php instead of rendering the web page UI.
* **Cause:** The PHP server was started incorrectly, or the file was opened directly from the filesystem (`file:///C:/...`) instead of via the HTTP port.
* **Resolution:**
  * Ensure you are visiting [http://localhost:8081](http://localhost:8081) in the browser, not double-clicking the files directly.

### 3.2 Error: `Call to undefined function curl_init()`
* **Symptom:** Page loads but displaying catalogs or log ins yields blank screens.
* **Cause:** The `php_curl` extension is disabled in your local PHP configuration.
* **Resolution:**
  1. Locate your `php.ini` file (usually inside `C:\xampp\php\php.ini`).
  2. Open the file and search for: `;extension=curl`
  3. Remove the semicolon at the beginning of the line to enable it: `extension=curl`
  4. Save the file and restart the microservice and frontend servers.

---

## 4. Frontend & Session Gate Loops

### 4.1 Error: Page immediately redirects back to Login Screen (Login Loop)
* **Symptom:** Logging in displays success, but clicking other tabs immediately redirects back to login.php.
* **Cause:** Session variables are not persisting because cookie parameters are blocked or PHP session directories are not writable.
* **Resolution:**
  * Ensure cookies are enabled in your browser.
  * Check the `session.save_path` parameter inside your local `php.ini` to verify it points to a valid, writable temp folder.
