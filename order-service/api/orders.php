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

    // Route request depending on HTTP verb and action parameters
    if ($action === 'create' && $method === 'POST') {
        $orderController->handleCreateOrder($_POST);
    } elseif (($action === 'update_status' || in_array($action, ['Pending', 'Shipped', 'Delivered', 'Cancelled'])) && $method === 'GET') {
        $orderController->handleUpdateStatus($_GET);
    } elseif (empty($action) && $method === 'GET') {
        $orderController->handleGetOrders($_GET);
    } else {
        jsonResponse([
            'status' => 'error',
            'message' => 'Bad Request: Action parameter or HTTP method mismatch.'
        ], 400);
    }

} catch (Exception $e) {
    jsonResponse([
        'status' => 'error',
        'message' => 'Internal Server Error: ' . $e->getMessage()
    ], 500);
}
