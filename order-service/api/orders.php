<?php
/**
 * order-service/api/orders.php
 * Entry point for orders creation, retrieval, and status changes.
 */

// 1. Initialize session safely if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Import dependencies
require_once __DIR__ . '/../../shared/database/connection_template.php';
require_once __DIR__ . '/../../shared/utils/response.php';
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../controllers/OrderController.php';

// Load configurations
$dbConfig = require __DIR__ . '/../config/config.php';

try {
    // 3. Connect to database and process request
    $pdo = createPdoConnection($dbConfig);
    $orderModel = new OrderModel($pdo);
    $orderController = new OrderController($orderModel);

    $action = $_GET['action'] ?? '';
    $method = $_SERVER['REQUEST_METHOD'];
    $id = isset($_GET['id']) ? $_GET['id'] : '';

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
        if ($action === 'update_status' || in_array($action, ['Pending', 'Shipped', 'Delivered', 'Cancelled'])) {
            $orderController->handleUpdateStatus($_GET);
        } elseif (is_numeric($id) && (int)$id > 0) {
            $orderController->handleGetOrderById((int)$id);
        } elseif (!empty($id)) {
            $order = $orderModel->getOrderByRef($id);
            if ($order) {
                jsonResponse(['status' => 'success', 'data' => $order], 200);
            } else {
                jsonResponse(['status' => 'error', 'message' => 'Order not found.'], 404);
            }
        } else {
            $orderController->handleGetOrders($_GET);
        }
    } elseif ($method === 'POST') {
        if ($action === 'create') {
            $orderController->handleCreateOrder($requestData);
        } else {
            // Also support POST without actions
            $orderController->handleCreateOrder($requestData);
        }
    } elseif ($method === 'PUT') {
        if (is_numeric($id) && (int)$id > 0) {
            $orderController->handleUpdateOrder((int)$id, $requestData);
        } else {
            jsonResponse(['status' => 'error', 'message' => 'Bad Request: Missing or invalid order ID.'], 400);
        }
    } elseif ($method === 'DELETE') {
        if (is_numeric($id) && (int)$id > 0) {
            $orderController->handleDeleteOrder((int)$id);
        } else {
            jsonResponse(['status' => 'error', 'message' => 'Bad Request: Missing or invalid order ID.'], 400);
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
