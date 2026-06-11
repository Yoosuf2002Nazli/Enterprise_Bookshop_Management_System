@echo off
REM ============================================================================
REM start-servers.bat - Start all microservices on separate ports (Windows)
REM ============================================================================
REM
REM This script starts all services on different ports:
REM   - Frontend:           http://localhost:8000
REM   - User Service:       http://localhost:8001
REM   - Catalog Service:    http://localhost:8002
REM   - Inventory Service:  http://localhost:8003
REM   - Order Service:      http://localhost:8004
REM   - Notification Service: http://localhost:8005
REM
REM Usage: start-servers.bat
REM ============================================================================

setlocal enabledelayedexpansion

REM Define port numbers
set FRONTEND_PORT=8000
set USER_PORT=8001
set CATALOG_PORT=8002
set INVENTORY_PORT=8003
set ORDER_PORT=8004
set NOTIFICATION_PORT=8005

REM Get the project root directory (one level up from this script)
set PROJECT_ROOT=%~dp0..

echo ============================================================================
echo PHP Built-in Servers Startup Script
echo ============================================================================
echo.
echo Starting microservices on separate ports...
echo.
echo   Frontend (router):      http://localhost:%FRONTEND_PORT%
echo   User Service:           http://localhost:%USER_PORT%
echo   Catalog Service:        http://localhost:%CATALOG_PORT%
echo   Inventory Service:      http://localhost:%INVENTORY_PORT%
echo   Order Service:          http://localhost:%ORDER_PORT%
echo   Notification Service:   http://localhost:%NOTIFICATION_PORT%
echo.
echo Press Ctrl+C in any window to stop that service.
echo ============================================================================
echo.

REM Verify PHP is available
php --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: PHP is not found in your system PATH.
    echo Please ensure PHP is installed and added to your system PATH.
    echo.
    pause
    exit /b 1
)

REM Verify MySQL is running
echo Checking MySQL connection...
mysql -u root -e "SELECT 1;" >nul 2>&1
if errorlevel 1 (
    echo WARNING: Cannot connect to MySQL. Please ensure MySQL is running.
    echo Services will start but database operations may fail.
    echo.
    pause
)

REM Start Frontend on port 8000
start "Frontend (8000)" cmd /k cd /d "%PROJECT_ROOT%" ^
    && php -S localhost:%FRONTEND_PORT% -t frontend servers/router.php

timeout /t 1 >nul

REM Start User Service on port 8001
start "User Service (8001)" cmd /k cd /d "%PROJECT_ROOT%\user-service" ^
    && php -S localhost:%USER_PORT%

timeout /t 1 >nul

REM Start Catalog Service on port 8002
start "Catalog Service (8002)" cmd /k cd /d "%PROJECT_ROOT%\catalog-service" ^
    && php -S localhost:%CATALOG_PORT%

timeout /t 1 >nul

REM Start Inventory Service on port 8003
start "Inventory Service (8003)" cmd /k cd /d "%PROJECT_ROOT%\inventory-service" ^
    && php -S localhost:%INVENTORY_PORT%

timeout /t 1 >nul

REM Start Order Service on port 8004
start "Order Service (8004)" cmd /k cd /d "%PROJECT_ROOT%\order-service" ^
    && php -S localhost:%ORDER_PORT%

timeout /t 1 >nul

REM Start Notification Service on port 8005
start "Notification Service (8005)" cmd /k cd /d "%PROJECT_ROOT%\notification-service" ^
    && php -S localhost:%NOTIFICATION_PORT%

echo.
echo ============================================================================
echo All services have been started!
echo ============================================================================
echo.
echo Access your services at:
echo   Frontend:           http://localhost:%FRONTEND_PORT%
echo   User Service API:   http://localhost:%USER_PORT%/api/index.php
echo   Catalog API:        http://localhost:%CATALOG_PORT%/api/books.php
echo   Inventory API:      http://localhost:%INVENTORY_PORT%/api/inventory.php
echo   Order API:          http://localhost:%ORDER_PORT%/api/orders.php
echo   Notification API:   http://localhost:%NOTIFICATION_PORT%/api/notify.php
echo.
echo Each service runs in a separate window. Close any window to stop that service.
echo ============================================================================
echo.
pause
