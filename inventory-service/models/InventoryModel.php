<?php
/**
 * InventoryModel
 * Data access layer for inventory_db.inventory table.
 */
class InventoryModel {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Retrieve all inventory records.
     */
    public function getAllInventory(): array {
        try {
            $stmt = $this->pdo->query("SELECT * FROM inventory ORDER BY id ASC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Retrieve records where stock is at or below the warning threshold.
     */
    public function getLowStock(): array {
        try {
            // Retrieve low stock rows sorted by amount ascending
            $stmt = $this->pdo->query("SELECT * FROM inventory WHERE stock <= threshold ORDER BY stock ASC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Retrieve inventory record by ISBN.
     */
    public function getByIsbn(string $isbn): ?array {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM inventory WHERE isbn = :isbn LIMIT 1");
            $stmt->execute([':isbn' => $isbn]);
            $item = $stmt->fetch();
            return $item ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Replenish inventory stock for a given ID.
     */
    public function restockItem(int $id, int $qty): bool {
        try {
            $stmt = $this->pdo->prepare("UPDATE inventory SET stock = stock + :qty WHERE id = :id");
            return $stmt->execute([
                ':qty' => $qty,
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Reduce stock for a given ISBN.
     * Prevents negative stock by checking if stock is greater than or equal to quantity.
     * Returns true on success, false if insufficient stock or db exception.
     */
    public function reduceStock(string $isbn, int $qty): bool {
        try {
            // First check if there is enough stock
            $item = $this->getByIsbn($isbn);
            if ($item === null || $item['stock'] < $qty) {
                return false;
            }

            // Perform the subtraction checking constraint inline
            $stmt = $this->pdo->prepare("
                UPDATE inventory 
                SET stock = stock - :qty 
                WHERE isbn = :isbn AND stock >= :min_qty
            ");
            return $stmt->execute([
                ':qty' => $qty,
                ':isbn' => $isbn,
                ':min_qty' => $qty
            ]) && $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Retrieve inventory record by ID.
     */
    public function getById(int $id): ?array {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM inventory WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $item = $stmt->fetch();
            return $item ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Create new inventory record.
     */
    public function createInventory(
        int $bookId,
        string $isbn,
        string $title,
        string $category,
        int $stock,
        int $threshold
    ): bool {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO inventory (book_id, isbn, title, category, stock, threshold) 
                VALUES (:book_id, :isbn, :title, :category, :stock, :threshold)
            ");
            return $stmt->execute([
                ':book_id' => $bookId,
                ':isbn' => $isbn,
                ':title' => $title,
                ':category' => $category,
                ':stock' => $stock,
                ':threshold' => $threshold
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Update an existing inventory record.
     */
    public function updateInventory(
        int $id,
        int $bookId,
        string $isbn,
        string $title,
        string $category,
        int $stock,
        int $threshold
    ): bool {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE inventory 
                SET book_id = :book_id, isbn = :isbn, title = :title, 
                    category = :category, stock = :stock, threshold = :threshold 
                WHERE id = :id
            ");
            return $stmt->execute([
                ':book_id' => $bookId,
                ':isbn' => $isbn,
                ':title' => $title,
                ':category' => $category,
                ':stock' => $stock,
                ':threshold' => $threshold,
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Delete an inventory record by ID.
     */
    public function deleteInventory(int $id): bool {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM inventory WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}

