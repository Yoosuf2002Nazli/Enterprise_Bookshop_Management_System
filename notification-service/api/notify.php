<?php
/**
 * notification-service/api/notify.php
 * Entry point for recording and listing system notification alerts.
 */

// 1. Import dependencies
require_once __DIR__ . '/../../shared/database/connection_template.php';
require_once __DIR__ . '/../../shared/utils/response.php';
require_once __DIR__ . '/../models/NotificationModel.php';
require_once __DIR__ . '/../controllers/NotificationController.php';

// Load configurations
$dbConfig = require __DIR__ . '/../config/config.php';

try {
    // 2. Connect to database and process request
    $pdo = createPdoConnection($dbConfig);
    $notificationModel = new NotificationModel($pdo);
    $notificationController = new NotificationController($notificationModel);

    $action = $_GET['action'] ?? '';
    $method = $_SERVER['REQUEST_METHOD'];
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    // Parse body for JSON input
    $requestData = $_POST;
    $rawBody = file_get_contents('php://input');
    if (!empty($rawBody)) {
        $jsonBody = json_decode($rawBody, true);
        if (is_array($jsonBody)) {
            $requestData = array_merge($requestData, $jsonBody);
        }
    }

    if ($method === 'GET') {
        if ($id > 0) {
            $notificationController->handleGetLogById($id);
        } else {
            $notificationController->handleGetLogs($_GET);
        }
    } elseif ($method === 'POST') {
        if ($action === 'log') {
            $notificationController->handleCreateLog($requestData);
        } else {
            // Also support post without action
            $notificationController->handleCreateLog($requestData);
        }
    } elseif ($method === 'PUT') {
        if ($id <= 0) {
            jsonResponse(['status' => 'error', 'message' => 'Bad Request: Missing or invalid notification ID.'], 400);
        } else {
            $notificationController->handleUpdateLog($id, $requestData);
        }
    } elseif ($method === 'DELETE') {
        if ($id <= 0) {
            jsonResponse(['status' => 'error', 'message' => 'Bad Request: Missing or invalid notification ID.'], 400);
        } else {
            $notificationController->handleDeleteLog($id);
        }
    } else {
        jsonResponse([
            'status' => 'error',
            'message' => 'Method Not Allowed.'
        ], 405);
    }

} catch (Exception $e) {
    jsonResponse([
        'status' => 'error',
        'message' => 'Internal Server Error: ' . $e->getMessage()
    ], 500);
}
