# Use-Case Diagram Placeholder

This document is a placeholder for the Use-Case Diagram of the Enterprise Bookshop Management System. The diagram defines the interactions between actors (Customer and Administrator) and system features.

> [!NOTE]
> This diagram will be updated with assets generated from our modeling tool. Below is a Mermaid representation of the primary use cases for preview.

## Use-Case Diagram Preview

```mermaid
graph TD
    %% Actors
    subgraph Actors
        Customer((Customer))
        Admin((Administrator))
    end

    %% Use Cases
    subgraph Use Cases (System Boundaries)
        UC1(Browse Catalog)
        UC2(Search & Filter Books)
        UC3(Register & Login)
        UC4(Checkout & Place Orders)
        UC5(View Personal Orders)
        
        UC6(Manage Catalog Items)
        UC7(Manage Inventory Levels)
        UC8(Monitor Admin Metrics)
        UC9(Transition Order Statuses)
    end

    %% Customer Associations
    Customer --> UC1
    Customer --> UC2
    Customer --> UC3
    Customer --> UC4
    Customer --> UC5

    %% Admin Associations
    Admin --> UC3
    Admin --> UC6
    Admin --> UC7
    Admin --> UC8
    Admin --> UC9
```
