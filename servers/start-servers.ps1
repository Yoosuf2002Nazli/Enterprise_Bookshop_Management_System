#!/usr/bin/env pwsh

################################################################################
# start-servers.ps1 - Start all microservices on separate ports (Windows PowerShell)
################################################################################
#
# This script starts all services on different ports:
#   - Frontend:           http://localhost:8000
#   - User Service:       http://localhost:8001
#   - Catalog Service:    http://localhost:8002
#   - Inventory Service:  http://localhost:8003
#   - Order Service:      http://localhost:8004
#   - Notification Service: http://localhost:8005
#
# Usage: .\start-servers.ps1
#
# Note: If you get execution policy error, run this first:
#   Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope Process
################################################################################

# Define port numbers
$FRONTEND_PORT = 8000
$USER_PORT = 8001
$CATALOG_PORT = 8002
$INVENTORY_PORT = 8003
$ORDER_PORT = 8004
$NOTIFICATION_PORT = 8005

# Get the project root directory (one level up from this script)
$SCRIPT_DIR = Split-Path -Parent $MyInvocation.MyCommand.Path
$PROJECT_ROOT = Split-Path -Parent $SCRIPT_DIR

# Colors
$GREEN = "`e[32m"
$RED = "`e[31m"
$YELLOW = "`e[33m"
$NC = "`e[0m"

Write-Host "============================================================================"
Write-Host "PHP Built-in Servers Startup Script"
Write-Host "============================================================================"
Write-Host ""
Write-Host "Starting microservices on separate ports..."
Write-Host ""
Write-Host "  Frontend (router):      http://localhost:$FRONTEND_PORT"
Write-Host "  User Service:           http://localhost:$USER_PORT"
Write-Host "  Catalog Service:        http://localhost:$CATALOG_PORT"
Write-Host "  Inventory Service:      http://localhost:$INVENTORY_PORT"
Write-Host "  Order Service:          http://localhost:$ORDER_PORT"
Write-Host "  Notification Service:   http://localhost:$NOTIFICATION_PORT"
Write-Host ""
Write-Host "Press Ctrl+C to stop a service."
Write-Host "============================================================================"
Write-Host ""

# Verify PHP is available
try {
    $phpVersion = php --version 2>$null
    if ($LASTEXITCODE -ne 0) {
        Write-Host "${RED}ERROR: PHP is not found in your system PATH.${NC}"
        Write-Host "Please ensure PHP is installed and added to your system PATH."
        Write-Host ""
        Read-Host "Press Enter to exit"
        exit 1
    }
    Write-Host "PHP version: $($phpVersion.Split("`n")[0])"
} catch {
    Write-Host "${RED}ERROR: PHP is not found in your system PATH.${NC}"
    Write-Host "Please ensure PHP is installed and added to your system PATH."
    Write-Host ""
    Read-Host "Press Enter to exit"
    exit 1
}

Write-Host ""

# Verify MySQL is running
Write-Host "Checking MySQL connection..."
try {
    $mysqlCheck = mysql -u root -e "SELECT 1;" 2>$null
    if ($LASTEXITCODE -eq 0) {
        Write-Host "${GREEN}MySQL is running.${NC}"
    } else {
        Write-Host "${YELLOW}WARNING: Cannot connect to MySQL.${NC}"
        Write-Host "Services will start but database operations may fail."
    }
} catch {
    Write-Host "${YELLOW}WARNING: Cannot connect to MySQL.${NC}"
    Write-Host "Services will start but database operations may fail."
}

Write-Host ""
Write-Host "Starting services..."
Write-Host ""

# Function to start a service
function Start-Service {
    param(
        [string]$Name,
        [int]$Port,
        [string]$Path,
        [string]$Router = ""
    )
    
    Write-Host "${GREEN}Starting $Name on port $Port...${NC}"
    
    $fullPath = Join-Path $PROJECT_ROOT $Path
    $cmd = "php -S localhost:$Port"
    
    if ($Router) {
        $cmd += " -t . $Router"
    }
    
    Push-Location $fullPath
    if ($Router) {
        $cmd = $cmd.Replace("-t .", "-t `"$fullPath`"")
    }
    Invoke-Expression $cmd | Out-Null
    Pop-Location
}

# Start all services in background
Write-Host "${GREEN}Starting Frontend on port $FRONTEND_PORT...${NC}"
$fe = Start-Process powershell -ArgumentList "-NoExit", "-Command", `
    "cd '$PROJECT_ROOT\frontend'; php -S localhost:$FRONTEND_PORT -t . '../servers/router.php'" `
    -WindowStyle Normal -PassThru

Start-Sleep -Seconds 1

Write-Host "${GREEN}Starting User Service on port $USER_PORT...${NC}"
$us = Start-Process powershell -ArgumentList "-NoExit", "-Command", `
    "cd '$PROJECT_ROOT\user-service'; php -S localhost:$USER_PORT" `
    -WindowStyle Normal -PassThru

Start-Sleep -Seconds 1

Write-Host "${GREEN}Starting Catalog Service on port $CATALOG_PORT...${NC}"
$cs = Start-Process powershell -ArgumentList "-NoExit", "-Command", `
    "cd '$PROJECT_ROOT\catalog-service'; php -S localhost:$CATALOG_PORT" `
    -WindowStyle Normal -PassThru

Start-Sleep -Seconds 1

Write-Host "${GREEN}Starting Inventory Service on port $INVENTORY_PORT...${NC}"
$is = Start-Process powershell -ArgumentList "-NoExit", "-Command", `
    "cd '$PROJECT_ROOT\inventory-service'; php -S localhost:$INVENTORY_PORT" `
    -WindowStyle Normal -PassThru

Start-Sleep -Seconds 1

Write-Host "${GREEN}Starting Order Service on port $ORDER_PORT...${NC}"
$os = Start-Process powershell -ArgumentList "-NoExit", "-Command", `
    "cd '$PROJECT_ROOT\order-service'; php -S localhost:$ORDER_PORT" `
    -WindowStyle Normal -PassThru

Start-Sleep -Seconds 1

Write-Host "${GREEN}Starting Notification Service on port $NOTIFICATION_PORT...${NC}"
$ns = Start-Process powershell -ArgumentList "-NoExit", "-Command", `
    "cd '$PROJECT_ROOT\notification-service'; php -S localhost:$NOTIFICATION_PORT" `
    -WindowStyle Normal -PassThru

Write-Host ""
Write-Host "============================================================================"
Write-Host "${GREEN}All services have been started!${NC}"
Write-Host "============================================================================"
Write-Host ""
Write-Host "Access your services at:"
Write-Host "  Frontend:           http://localhost:$FRONTEND_PORT"
Write-Host "  User Service API:   http://localhost:$USER_PORT/api/index.php"
Write-Host "  Catalog API:        http://localhost:$CATALOG_PORT/api/books.php"
Write-Host "  Inventory API:      http://localhost:$INVENTORY_PORT/api/inventory.php"
Write-Host "  Order API:          http://localhost:$ORDER_PORT/api/orders.php"
Write-Host "  Notification API:   http://localhost:$NOTIFICATION_PORT/api/notify.php"
Write-Host ""
Write-Host "Service windows have opened. Close any window to stop that service."
Write-Host "============================================================================"
Write-Host ""

Write-Host "Main script running. Press Ctrl+C to exit this window."
Write-Host "(Individual service windows will remain open.)"
Write-Host ""

# Keep main window open
Read-Host "Press Enter when finished"
