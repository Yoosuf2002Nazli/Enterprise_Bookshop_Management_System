<?php
/**
 * NotificationModel
 * Data access layer for notification_db.notifications table.
 */
class NotificationModel {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Create a notification log entry.
     */
    public function createLog(string $type, string $message, ?string $referenceId): bool {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO notifications (type, message, reference_id) 
                VALUES (:type, :message, :reference_id)
            ");
            return $stmt->execute([
                ':type' => $type,
                ':message' => $message,
                ':reference_id' => $referenceId
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Fetch recent notification logs.
     */
    public function getRecentLogs(int $limit = 20): array {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM notifications ORDER BY created_at DESC LIMIT :limit");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Retrieve a notification log by ID.
     */
    public function getLogById(int $id): ?array {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM notifications WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $log = $stmt->fetch();
            return $log ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Update an existing notification log.
     */
    public function updateLog(int $id, string $type, string $message, ?string $referenceId): bool {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE notifications 
                SET type = :type, message = :message, reference_id = :reference_id 
                WHERE id = :id
            ");
            return $stmt->execute([
                ':type' => $type,
                ':message' => $message,
                ':reference_id' => $referenceId,
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Delete a notification log by ID.
     */
    public function deleteLog(int $id): bool {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM notifications WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}

