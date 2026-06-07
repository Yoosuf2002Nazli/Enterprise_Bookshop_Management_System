<?php
// inventory-service/models/StockLevel.php
/**
 * StockLevel Model - Data Access Layer for Inventory
 * Manages stock quantities, warehouses, and reorder points
 */

class StockLevel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get stock level for a specific book and warehouse
     * @param int $bookId Book ID
     * @param int $warehouseId Warehouse ID (optional)
     * @return array|null Stock record
     */
    public function getByBookAndWarehouse($bookId, $warehouseId = null) {
        if ($warehouseId) {
            $stmt = $this->pdo->prepare(
                "SELECT * FROM stock_levels WHERE book_id = ? AND warehouse_id = ?"
            );
            $stmt->execute([$bookId, $warehouseId]);
        } else {
            // Get total across all warehouses
            $stmt = $this->pdo->prepare(
                "SELECT book_id, SUM(quantity_on_hand) as quantity_on_hand, 
                        AVG(reorder_point) as reorder_point
                 FROM stock_levels WHERE book_id = ? GROUP BY book_id"
            );
            $stmt->execute([$bookId]);
        }
        return $stmt->fetch();
    }

    /**
     * Get all stock levels with warehouse info
     * @param array $filters Optional filters
     * @return array List of stock records
     */
    public function getAll($filters = []) {
        $query = "SELECT sl.*, w.name as warehouse_name, w.location 
                  FROM stock_levels sl
                  JOIN warehouses w ON sl.warehouse_id = w.id
                  WHERE 1=1";
        $params = [];

        if (isset($filters['warehouse_id'])) {
            $query .= " AND sl.warehouse_id = ?";
            $params[] = $filters['warehouse_id'];
        }

        if (isset($filters['low_stock'])) {
            $query .= " AND sl.quantity_on_hand <= sl.reorder_point";
        }

        if (isset($filters['out_of_stock'])) {
            $query .= " AND sl.quantity_on_hand = 0";
        }

        $query .= " ORDER BY sl.book_id, sl.warehouse_id";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Get low stock items
     * @return array List of items below reorder point
     */
    public function getLowStockItems() {
        $stmt = $this->pdo->query(
            "SELECT sl.*, w.name as warehouse_name FROM stock_levels sl
             JOIN warehouses w ON sl.warehouse_id = w.id
             WHERE sl.quantity_on_hand <= sl.reorder_point
             ORDER BY sl.quantity_on_hand ASC"
        );
        return $stmt->fetchAll();
    }

    /**
     * Get out of stock items
     * @return array List of items with zero quantity
     */
    public function getOutOfStockItems() {
        $stmt = $this->pdo->query(
            "SELECT sl.*, w.name as warehouse_name FROM stock_levels sl
             JOIN warehouses w ON sl.warehouse_id = w.id
             WHERE sl.quantity_on_hand = 0
             ORDER BY sl.book_id"
        );
        return $stmt->fetchAll();
    }

    /**
     * Update stock quantity
     * @param int $bookId Book ID
     * @param int $warehouseId Warehouse ID
     * @param int $newQuantity New quantity
     * @return bool Success status
     */
    public function updateQuantity($bookId, $warehouseId, $newQuantity) {
        $stmt = $this->pdo->prepare(
            "UPDATE stock_levels SET quantity_on_hand = ? WHERE book_id = ? AND warehouse_id = ?"
        );
        return $stmt->execute([$newQuantity, $bookId, $warehouseId]);
    }

    /**
     * Increment stock (add quantity)
     * @param int $bookId Book ID
     * @param int $warehouseId Warehouse ID
     * @param int $quantity Quantity to add
     * @return bool Success status
     */
    public function addStock($bookId, $warehouseId, $quantity) {
        $stmt = $this->pdo->prepare(
            "UPDATE stock_levels SET quantity_on_hand = quantity_on_hand + ? 
             WHERE book_id = ? AND warehouse_id = ?"
        );
        return $stmt->execute([$quantity, $bookId, $warehouseId]);
    }

    /**
     * Decrement stock (remove quantity)
     * @param int $bookId Book ID
     * @param int $warehouseId Warehouse ID
     * @param int $quantity Quantity to remove
     * @return bool Success status
     */
    public function removeStock($bookId, $warehouseId, $quantity) {
        // Check if sufficient stock exists
        $current = $this->getByBookAndWarehouse($bookId, $warehouseId);
        if (!$current || $current['quantity_on_hand'] < $quantity) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            "UPDATE stock_levels SET quantity_on_hand = quantity_on_hand - ? 
             WHERE book_id = ? AND warehouse_id = ?"
        );
        return $stmt->execute([$quantity, $bookId, $warehouseId]);
    }

    /**
     * Update reorder point and quantity
     * @param int $bookId Book ID
     * @param int $warehouseId Warehouse ID
     * @param int $reorderPoint New reorder point
     * @param int $reorderQuantity Quantity to order
     * @return bool Success status
     */
    public function updateReorderPoint($bookId, $warehouseId, $reorderPoint, $reorderQuantity) {
        $stmt = $this->pdo->prepare(
            "UPDATE stock_levels SET reorder_point = ?, reorder_quantity = ? 
             WHERE book_id = ? AND warehouse_id = ?"
        );
        return $stmt->execute([$reorderPoint, $reorderQuantity, $bookId, $warehouseId]);
    }

    /**
     * Record restock timestamp
     * @param int $bookId Book ID
     * @param int $warehouseId Warehouse ID
     * @return bool Success status
     */
    public function recordRestock($bookId, $warehouseId) {
        $stmt = $this->pdo->prepare(
            "UPDATE stock_levels SET last_restocked_at = CURRENT_TIMESTAMP 
             WHERE book_id = ? AND warehouse_id = ?"
        );
        return $stmt->execute([$bookId, $warehouseId]);
    }

    /**
     * Get stock summary by warehouse
     * @return array Summary statistics
     */
    public function getWarehouseSummary() {
        $stmt = $this->pdo->query(
            "SELECT w.id, w.name, w.location,
                    COUNT(DISTINCT sl.book_id) as total_books,
                    SUM(sl.quantity_on_hand) as total_quantity,
                    SUM(CASE WHEN sl.quantity_on_hand = 0 THEN 1 ELSE 0 END) as out_of_stock_count,
                    SUM(CASE WHEN sl.quantity_on_hand <= sl.reorder_point THEN 1 ELSE 0 END) as low_stock_count
             FROM warehouses w
             LEFT JOIN stock_levels sl ON w.id = sl.warehouse_id
             WHERE w.is_active = 1
             GROUP BY w.id, w.name, w.location
             ORDER BY w.name"
        );
        return $stmt->fetchAll();
    }

    /**
     * Create stock level entry
     * @param array $data Stock data
     * @return int|false Stock ID
     */
    public function create($data) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO stock_levels (book_id, warehouse_id, quantity_on_hand, reorder_point, reorder_quantity)
             VALUES (?, ?, ?, ?, ?)"
        );

        $result = $stmt->execute([
            $data['book_id'] ?? null,
            $data['warehouse_id'] ?? null,
            $data['quantity_on_hand'] ?? 0,
            $data['reorder_point'] ?? 10,
            $data['reorder_quantity'] ?? 20
        ]);

        return $result ? $this->pdo->lastInsertId() : false;
    }
}