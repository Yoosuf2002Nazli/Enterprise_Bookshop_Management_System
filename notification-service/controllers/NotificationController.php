<?php
/**
 * NotificationController
 * Handles logging and retrieving service notifications.
 */
class NotificationController {
    private NotificationModel $model;

    public function __construct(NotificationModel $model) {
        $this->model = $model;
    }

    /**
     * Handle logging new notification alerts.
     */
    public function handleCreateLog(array $post): void {
        $type = trim($post['type'] ?? '');
        $message = trim($post['message'] ?? '');
        $referenceId = isset($post['reference_id']) ? trim($post['reference_id']) : null;

        // 1. Validate required fields
        if (empty($type) || empty($message)) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Type and message are required parameters.'
            ], 400);
            return;
        }

        // 2. Perform table insert
        $success = $this->model->createLog($type, $message, $referenceId);
        if ($success) {
            jsonResponse([
                'status' => 'success',
                'message' => 'Notification log recorded successfully.'
            ], 201);
        } else {
            jsonResponse([
                'status' => 'error',
                'message' => 'Failed to record notification log.'
            ], 500);
        }
    }

    /**
     * Fetch recent notification log rows.
     */
    public function handleGetLogs(array $get): void {
        $limit = isset($get['limit']) ? (int)$get['limit'] : 20;

        if ($limit <= 0) {
            $limit = 20;
        }

        $logs = $this->model->getRecentLogs($limit);
        
        jsonResponse([
            'status' => 'success',
            'data' => $logs
        ], 200);
    }

    /**
     * Handles fetching a single notification log by ID.
     */
    public function handleGetLogById(int $id): void {
        $log = $this->model->getLogById($id);
        if ($log === null) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Notification log not found.'
            ], 404);
            return;
        }
        jsonResponse([
            'status' => 'success',
            'data' => $log
        ], 200);
    }

    /**
     * Handles updating an existing notification log.
     */
    public function handleUpdateLog(int $id, array $data): void {
        $type = trim($data['type'] ?? '');
        $message = trim($data['message'] ?? '');
        $referenceId = isset($data['reference_id']) ? trim($data['reference_id']) : null;

        if (empty($type) || empty($message)) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Type and message are required fields.'
            ], 400);
            return;
        }

        $log = $this->model->getLogById($id);
        if ($log === null) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Notification log not found.'
            ], 404);
            return;
        }

        $success = $this->model->updateLog($id, $type, $message, $referenceId);
        if ($success) {
            jsonResponse([
                'status' => 'success',
                'message' => 'Notification log updated successfully.'
            ], 200);
        } else {
            jsonResponse([
                'status' => 'error',
                'message' => 'Failed to update notification log.'
            ], 500);
        }
    }

    /**
     * Handles deleting a notification log.
     */
    public function handleDeleteLog(int $id): void {
        $log = $this->model->getLogById($id);
        if ($log === null) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Notification log not found.'
            ], 404);
            return;
        }

        $success = $this->model->deleteLog($id);
        if ($success) {
            jsonResponse([
                'status' => 'success',
                'message' => 'Notification log deleted successfully.'
            ], 200);
        } else {
            jsonResponse([
                'status' => 'error',
                'message' => 'Failed to delete notification log.'
            ], 500);
        }
    }
}

