# Sequence Diagram Placeholder

This document is a placeholder for the Sequence Diagram of the Enterprise Bookshop Management System. The diagram traces the interaction and execution flow across components during a transaction, specifically during checkout/ordering.

> [!NOTE]
> This diagram will be updated with assets generated from our modeling tool. Below is a Mermaid representation of the checkout and stock reduction sequence for preview.

## Checkout Sequence Preview

```mermaid
sequenceDiagram
    autonumber
    actor Customer as Customer (Browser)
    participant FE as Frontend App
    participant OS as Order Service (8004)
    participant CS as Catalog Service (8002)
    participant IS as Inventory Service (8003)
    participant DB as MySQL Databases

    Customer->>FE: Click "Checkout" (Form POST)
    FE->>OS: Invoke Create Order (POST orders.php)
    activate OS
    OS->>CS: Fetch Book / ISBN (via BookModel)
    activate CS
    CS->>DB: Query catalog_db
    DB-->>CS: Book Details (ISBN, Price, Title)
    CS-->>OS: Return Book Details
    deactivate CS

    OS->>OS: Validate Details & Compute Total
    OS->>DB: Insert Order into order_db (Status: Pending)

    OS->>IS: Call Reduce Stock (via InventoryModel)
    activate IS
    IS->>DB: Update inventory_db (Reduce stock by qty)
    alt Stock Available
        DB-->>IS: Update Success
        IS-->>OS: Stock Reduced (true)
    else Stock Out
        DB-->>IS: Update Failure
        IS-->>OS: Stock Reduced (false)
        OS->>DB: Update Order Status to "Cancelled"
    end
    deactivate IS

    OS-->>FE: Return Order Status JSON
    deactivate OS
    FE-->>Customer: Display Order Confirmation
```
