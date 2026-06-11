# Deployment Diagram Placeholder

This document is a placeholder for the Deployment Diagram of the Enterprise Bookshop Management System. The diagram shows the physical hardware nodes, network topologies, and execution environments onto which the system is deployed.

> [!NOTE]
> This diagram will be updated with assets generated from our modeling tool. Below is a Mermaid representation of our port-separated deployment architecture.

## Deployment Diagram Preview

```mermaid
graph TD
    subgraph Client Device [Client Machine]
        Browser[Web Browser]
    end

    subgraph Server Host [Local Development Server]
        subgraph Web Server [Apache Instance]
            FE[Frontend Web App - Port 80]
        end

        subgraph PHP Built-in Servers [Separate CLI Runtimes]
            US[User Service - Port 8001]
            CS[Catalog Service - Port 8002]
            IS[Inventory Service - Port 8003]
            OS[Order Service - Port 8004]
            NS[Notification Service - Port 8005]
        end

        subgraph Database Engine [MySQL Instance - Port 3306]
            UDB[(user_db)]
            CDB[(catalog_db)]
            IDB[(inventory_db)]
            ODB[(order_db)]
            NDB[(notification_db)]
        end
    end

    %% Network pathways
    Browser -->|HTTP on Port 80| FE
    Browser -->|HTTP on Ports 8001-8005 / Postman| US
    Browser -->|HTTP on Ports 8001-8005 / Postman| CS
    Browser -->|HTTP on Ports 8001-8005 / Postman| IS
    Browser -->|HTTP on Ports 8001-8005 / Postman| OS
    Browser -->|HTTP on Ports 8001-8005 / Postman| NS

    %% DB Connections
    US -->|JDBC/PDO localhost:3306| UDB
    CS -->|JDBC/PDO localhost:3306| CDB
    IS -->|JDBC/PDO localhost:3306| IDB
    OS -->|JDBC/PDO localhost:3306| ODB
    NS -->|JDBC/PDO localhost:3306| NDB
```
