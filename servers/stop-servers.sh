#!/bin/bash

################################################################################
# stop-servers.sh - Stop all running PHP servers (Linux/Mac)
################################################################################
#
# This script gracefully stops all PHP built-in servers by finding and
# terminating processes running on ports 8000-8005.
#
# Usage: ./stop-servers.sh
################################################################################

# Define port numbers
FRONTEND_PORT=8000
USER_PORT=8001
CATALOG_PORT=8002
INVENTORY_PORT=8003
ORDER_PORT=8004
NOTIFICATION_PORT=8005

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo "============================================================================"
echo "PHP Built-in Servers Shutdown Script"
echo "============================================================================"
echo ""

# Function to stop service on a specific port
stop_service_on_port() {
    local port=$1
    local name=$2
    
    # Find process on the port
    local pid=$(lsof -ti:$port 2>/dev/null)
    
    if [ -n "$pid" ]; then
        echo -e "${YELLOW}Stopping $name (port $port)...${NC}"
        kill -TERM $pid 2>/dev/null
        
        # Wait a moment for graceful shutdown
        sleep 1
        
        # Check if still running
        if kill -0 $pid 2>/dev/null; then
            echo -e "${RED}Force killing $name (PID: $pid)...${NC}"
            kill -9 $pid 2>/dev/null
        else
            echo -e "${GREEN}$name stopped successfully.${NC}"
        fi
    else
        echo "No service running on port $port."
    fi
}

# Stop all services
stop_service_on_port $FRONTEND_PORT "Frontend"
stop_service_on_port $USER_PORT "User Service"
stop_service_on_port $CATALOG_PORT "Catalog Service"
stop_service_on_port $INVENTORY_PORT "Inventory Service"
stop_service_on_port $ORDER_PORT "Order Service"
stop_service_on_port $NOTIFICATION_PORT "Notification Service"

echo ""
echo "============================================================================"
echo -e "${GREEN}All services have been stopped.${NC}"
echo "============================================================================"
echo ""
