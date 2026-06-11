# Component Diagram Placeholder

This document is a placeholder for the Component Diagram of the Enterprise Bookshop Management System. The diagram represents the software components, their interfaces, and dependencies in a microservices architecture.

> [!NOTE]
> This diagram will be updated with assets generated from our modeling tool. Below is a Mermaid representation of the components for preview and design alignment.

## Microservices Component Preview

```mermaid
graph TD
    subgraph Client Layer
        Browser[Client Browser]
    end

    subgraph Frontend Web App
        FE[Frontend Website]
    end

    subgraph Microservices Layer
        US[User Service - Port 8001]
        CS[Catalog Service - Port 8002]
        IS[Inventory Service - Port 8003]
        OS[Order Service - Port 8004]
        NS[Notification Service - Port 8005]
    end

    subgraph Database Layer
        UDB[(User DB)]
        CDB[(Catalog DB)]
        IDB[(Inventory DB)]
        ODB[(Order DB)]
        NDB[(Notification DB)]
    end

    subgraph Shared Libraries
        SH[Shared Database Wrapper & Utilities]
    end

    Browser -->|HTTP/HTTPS| FE
    FE -->|Direct API Imports / CLI HTTP Request| US
    FE -->|Direct API Imports / CLI HTTP Request| CS
    FE -->|Direct API Imports / CLI HTTP Request| IS
    FE -->|Direct API Imports / CLI HTTP Request| OS
    FE -->|Direct API Imports / CLI HTTP Request| NS

    %% Service Database bindings
    US ---> UDB
    CS ---> CDB
    IS ---> IDB
    OS ---> ODB
    NS ---> NDB

    %% Shared utility references
    US -.-> SH
    CS -.-> SH
    IS -.-> SH
    OS -.-> SH
    NS -.-> SH

    %% Cross-service dependencies (Order to Catalog/Inventory)
    OS -.->|Requires Model & DB Config| CS
    OS -.->|Requires Model & DB Config| IS
```
