@echo off
echo ===================================================
echo   Starting Bookshop Management System Microservices
echo ===================================================

cd %~dp0\..

:: Determine PHP executable path
set PHP_BIN=php
where /q php
if %errorlevel% neq 0 (
    if exist "C:\xampp\php\php.exe" (
        set PHP_BIN=C:\xampp\php\php.exe
    ) else (
        echo ERROR: PHP executable could not be found globally or in C:\xampp\php\
        echo Please ensure PHP is installed and in your PATH or at C:\xampp\php\
        pause
        exit /b 1
    )
)

echo Starting User Service on port 8001...
start "User Service (Port 8001)" cmd /c ""%PHP_BIN%" -S localhost:8001 -t user-service"

echo Starting Catalog Service on port 8002...
start "Catalog Service (Port 8002)" cmd /c ""%PHP_BIN%" -S localhost:8002 -t catalog-service"

echo Starting Inventory Service on port 8003...
start "Inventory Service (Port 8003)" cmd /c ""%PHP_BIN%" -S localhost:8003 -t inventory-service"

echo Starting Order Service on port 8004...
start "Order Service (Port 8004)" cmd /c ""%PHP_BIN%" -S localhost:8004 -t order-service"

echo Starting Notification Service on port 8005...
start "Notification Service (Port 8005)" cmd /c ""%PHP_BIN%" -S localhost:8005 -t notification-service"

echo.
echo All microservices have been launched in separate terminal windows.
echo To shut down all services, close their respective windows or run:
echo taskkill /F /IM php.exe
echo.
pause
