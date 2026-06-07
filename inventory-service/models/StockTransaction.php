<?php
// inventory-service/models/StockTransaction.php
/**
 * StockTransaction Model - Audit Trail for Stock Changes
 * Logs all inventory movements for tracking and reporting
 */

class StockTransaction {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Record a stock transaction
     * @param array $data Transaction data
     * @return int|false Transaction ID
     */
    public function create($data) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO stock_transactions (book_id, warehouse_id, transaction_type, quantity, reason, reference_id, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        $result = $stmt->execute([
            $data['book_id'] ?? null,
            $data['warehouse_id'] ?? null,
            $data['transaction_type'] ?? 'ADJUSTMENT', // IN, OUT, ADJUSTMENT, LOSS
            $data['quantity'] ?? 0,
            $data['reason'] ?? null,
            $data['reference_id'] ?? null,
            $data['notes'] ?? null
        ]);

        return $result ? $this->pdo->lastInsertId() : false;
    }

    /**
     * Get all transactions
     * @param array $filters Filter options
     * @return array List of transactions
     */
    public function getAll($filters = []) {
        $query = "SELECT * FROM stock_transactions WHERE 1=1";
        $params = [];

        if (isset($filters['book_id'])) {
            $query .= " AND book_id = ?";
            $params[] = $filters['book_id'];
        }

        if (isset($filters['warehouse_id'])) {
            $query .= " AND warehouse_id = ?";
            $params[] = $filters['warehouse_id'];
        }

        if (isset($filters['type'])) {
            $query .= " AND transaction_type = ?";
            $params[] = $filters['type'];
        }

        if (isset($filters['start_date'])) {
            $query .= " AND DATE(created_at) >= DATE(?)";
            $params[] = $filters['start_date'];
        }

        if (isset($filters['end_date'])) {
            $query .= " AND DATE(created_at) <= DATE(?)";
            $params[] = $filters['end_date'];
        }

        $query .= " ORDER BY created_at DESC";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Get transaction by ID
     * @param int $id Transaction ID
     * @return array|null Transaction record
     */
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM stock_transactions WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Get transaction history for a book
     * @param int $bookId Book ID
     * @param int $limit Records limit
     * @return array Transactions for book
     */
    public function getByBook($bookId, $limit = 50) {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM stock_transactions WHERE book_id = ? ORDER BY created_at DESC LIMIT ?"
        );
        $stmt->execute([$bookId, $limit]);
        return $stmt->fetchAll();
    }

    /**
     * Get warehouse activity
     * @param int $warehouseId Warehouse ID
     * @param int $limit Records limit
     * @return array Recent transactions
     */
    public function getByWarehouse($warehouseId, $limit = 50) {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM stock_transactions WHERE warehouse_id = ? ORDER BY created_at DESC LIMIT ?"
        );
        $stmt->execute([$warehouseId, $limit]);
        return $stmt->fetchAll();
    }

    /**
     * Get transaction summary by type
     * @param int $warehouseId Warehouse ID (optional)
     * @return array Summary data
     */
    public function getSummaryByType($warehouseId = null) {
        $query = "SELECT transaction_type, COUNT(*) as count, SUM(quantity) as total_quantity
                  FROM stock_transactions
                  WHERE 1=1";
        $params = [];

        if ($warehouseId) {
            $query .= " AND warehouse_id = ?";
            $params[] = $warehouseId;
        }

        $query .= " GROUP BY transaction_type";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Get recent critical transactions
     * @param int $days Number of days to look back
     * @return array Recent significant transactions
     */
    public function getRecentCritical($days = 7) {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM stock_transactions 
             WHERE transaction_type IN ('LOSS', 'OUT')
             AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
             ORDER BY created_at DESC
             LIMIT 50"
        );
        $stmt->execute([$days]);
        return $stmt->fetchAll();
    }
}