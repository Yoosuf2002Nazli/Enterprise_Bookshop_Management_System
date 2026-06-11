@echo off
echo ===================================================
echo   Starting MySQL Database Server for Bookshop System
echo ===================================================

if exist "C:\xampp\mysql\bin\mysqld.exe" (
    echo Starting MySQL Database Server...
    start "MySQL Database Server" "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini" --console
    echo MySQL has been launched in a separate window.
) else (
    echo ERROR: MySQL executable not found at C:\xampp\mysql\bin\mysqld.exe
    echo Please ensure XAMPP is installed at default C:\xampp location.
    pause
)
