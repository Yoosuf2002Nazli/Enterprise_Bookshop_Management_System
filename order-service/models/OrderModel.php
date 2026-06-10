<?php
/**
 * OrderModel
 * Data access layer for order_db.orders and order_items tables.
 */
class OrderModel {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Retrieve all orders with their respective line items attached.
     */
    public function getAllOrders(): array {
        try {
            // Fetch all orders sorted by creation date descending
            $stmt = $this->pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
            $orders = $stmt->fetchAll();

            // Populate each order with its item details
            foreach ($orders as &$order) {
                $order['items'] = $this->getOrderItems($order['order_ref']);
            }
            unset($order); // break pointer reference

            return $orders;
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Retrieve details of a single order by its reference identifier.
     */
    public function getOrderByRef(string $orderRef): ?array {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE order_ref = :order_ref LIMIT 1");
            $stmt->execute([':order_ref' => $orderRef]);
            $order = $stmt->fetch();

            if ($order) {
                $order['items'] = $this->getOrderItems($orderRef);
                return $order;
            }
            return null;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Create a new order and insert all associated line items inside a database transaction.
     */
    public function createOrder(
        string $orderRef,
        string $customer,
        string $email,
        float $total,
        array $items
    ): bool {
        try {
            // Start transaction
            $this->pdo->beginTransaction();

            // 1. Insert order details
            $stmtOrder = $this->pdo->prepare("
                INSERT INTO orders (order_ref, customer, email, total, status) 
                VALUES (:order_ref, :customer, :email, :total, 'Pending')
            ");
            $stmtOrder->execute([
                ':order_ref' => $orderRef,
                ':customer' => $customer,
                ':email' => $email,
                ':total' => $total
            ]);

            // 2. Insert line items
            $stmtItem = $this->pdo->prepare("
                INSERT INTO order_items (order_ref, title, qty, price) 
                VALUES (:order_ref, :title, :qty, :price)
            ");
            
            foreach ($items as $item) {
                $stmtItem->execute([
                    ':order_ref' => $orderRef,
                    ':title' => $item['title'],
                    ':qty' => (int)$item['qty'],
                    ':price' => (float)$item['price']
                ]);
            }

            // Commit transaction
            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            // Rollback changes on any failure
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return false;
        }
    }

    /**
     * Update status flag for a given order reference.
     */
    public function updateOrderStatus(string $orderRef, string $status): bool {
        $allowedStatuses = ['Pending', 'Shipped', 'Delivered', 'Cancelled'];
        if (!in_array($status, $allowedStatuses)) {
            return false;
        }

        try {
            $stmt = $this->pdo->prepare("UPDATE orders SET status = :status WHERE order_ref = :order_ref");
            return $stmt->execute([
                ':status' => $status,
                ':order_ref' => $orderRef
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Helper to fetch items associated with an order reference.
     */
    private function getOrderItems(string $orderRef): array {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM order_items WHERE order_ref = :order_ref");
            $stmt->execute([':order_ref' => $orderRef]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Retrieve an order details by ID (including items).
     */
    public function getOrderById(int $id): ?array {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $order = $stmt->fetch();

            if ($order) {
                $order['items'] = $this->getOrderItems($order['order_ref']);
                return $order;
            }
            return null;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Update order details (customer, email, total, status).
     */
    public function updateOrder(
        int $id,
        string $customer,
        string $email,
        float $total,
        string $status
    ): bool {
        $allowedStatuses = ['Pending', 'Shipped', 'Delivered', 'Cancelled'];
        if (!in_array($status, $allowedStatuses)) {
            return false;
        }

        try {
            $stmt = $this->pdo->prepare("
                UPDATE orders 
                SET customer = :customer, email = :email, total = :total, status = :status 
                WHERE id = :id
            ");
            return $stmt->execute([
                ':customer' => $customer,
                ':email' => $email,
                ':total' => $total,
                ':status' => $status,
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Delete/Cancel order (soft cancellation by setting status to Cancelled).
     */
    public function deleteOrder(int $id): bool {
        try {
            $stmt = $this->pdo->prepare("UPDATE orders SET status = 'Cancelled' WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}

