#!/bin/bash

################################################################################
# start-servers.sh - Start all microservices on separate ports (Linux/Mac)
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
# Usage: ./start-servers.sh
################################################################################

# Define port numbers
FRONTEND_PORT=8000
USER_PORT=8001
CATALOG_PORT=8002
INVENTORY_PORT=8003
ORDER_PORT=8004
NOTIFICATION_PORT=8005

# Get the project root directory (one level up from this script)
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "============================================================================"
echo "PHP Built-in Servers Startup Script"
echo "============================================================================"
echo ""
echo "Starting microservices on separate ports..."
echo ""
echo "  Frontend (router):      http://localhost:$FRONTEND_PORT"
echo "  User Service:           http://localhost:$USER_PORT"
echo "  Catalog Service:        http://localhost:$CATALOG_PORT"
echo "  Inventory Service:      http://localhost:$INVENTORY_PORT"
echo "  Order Service:          http://localhost:$ORDER_PORT"
echo "  Notification Service:   http://localhost:$NOTIFICATION_PORT"
echo ""
echo "Press Ctrl+C to stop all services."
echo "============================================================================"
echo ""

# Verify PHP is available
if ! command -v php &> /dev/null; then
    echo -e "${RED}ERROR: PHP is not found in your system PATH.${NC}"
    echo "Please ensure PHP is installed and available in your PATH."
    echo ""
    exit 1
fi

echo "PHP version: $(php --version | head -n 1)"
echo ""

# Verify MySQL is running
echo "Checking MySQL connection..."
if ! mysql -u root -e "SELECT 1;" &> /dev/null; then
    echo -e "${YELLOW}WARNING: Cannot connect to MySQL.${NC}"
    echo "Please ensure MySQL is running. Services will start but may fail to connect to databases."
    echo ""
    read -p "Continue anyway? (y/n) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 1
    fi
fi

echo ""
echo "Starting services..."
echo ""

# Function to start a service
start_service() {
    local name=$1
    local port=$2
    local path=$3
    local router=$4
    
    if [ -z "$router" ]; then
        # Service without router
        echo -e "${GREEN}Starting $name on port $port...${NC}"
        cd "$PROJECT_ROOT/$path"
        php -S localhost:$port > /dev/null 2>&1 &
    else
        # Service with router (frontend)
        echo -e "${GREEN}Starting $name on port $port...${NC}"
        cd "$PROJECT_ROOT/$path"
        php -S localhost:$port -t . $router > /dev/null 2>&1 &
    fi
}

# Start all services
start_service "Frontend" "$FRONTEND_PORT" "frontend" "servers/router.php"
sleep 1

start_service "User Service" "$USER_PORT" "user-service"
sleep 1

start_service "Catalog Service" "$CATALOG_PORT" "catalog-service"
sleep 1

start_service "Inventory Service" "$INVENTORY_PORT" "inventory-service"
sleep 1

start_service "Order Service" "$ORDER_PORT" "order-service"
sleep 1

start_service "Notification Service" "$NOTIFICATION_PORT" "notification-service"
sleep 1

echo ""
echo "============================================================================"
echo -e "${GREEN}All services have been started!${NC}"
echo "============================================================================"
echo ""
echo "Access your services at:"
echo "  Frontend:           http://localhost:$FRONTEND_PORT"
echo "  User Service API:   http://localhost:$USER_PORT/api/index.php"
echo "  Catalog API:        http://localhost:$CATALOG_PORT/api/books.php"
echo "  Inventory API:      http://localhost:$INVENTORY_PORT/api/inventory.php"
echo "  Order API:          http://localhost:$ORDER_PORT/api/orders.php"
echo "  Notification API:   http://localhost:$NOTIFICATION_PORT/api/notify.php"
echo ""
echo "============================================================================"
echo ""

# Wait for Ctrl+C
trap 'echo ""; echo "Shutting down services..."; pkill -P $$; exit 0' INT

# Keep the script running
while true; do
    sleep 1
done
