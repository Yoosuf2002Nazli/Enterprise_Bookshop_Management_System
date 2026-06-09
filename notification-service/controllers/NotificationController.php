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
}
