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

    // Parse body for JSON input first so we can read parameters from it
    $requestData = $_POST;
    $rawBody = file_get_contents('php://input');
    if (!empty($rawBody)) {
        $jsonBody = json_decode($rawBody, true);
        if (is_array($jsonBody)) {
            $requestData = array_merge($requestData, $jsonBody);
        }
    }

    $action = $_GET['action'] ?? '';
    $method = $_SERVER['REQUEST_METHOD'];
    
    // Resolve identifier from query parameters or request body (supporting id and order_id)
    $idVal = $_GET['id'] ?? $_GET['order_id'] ?? $requestData['id'] ?? $requestData['order_id'] ?? '';

    // Convert string order reference (like ORD-...) to numeric database ID if needed
    $numericId = 0;
    if (is_numeric($idVal)) {
        $numericId = (int)$idVal;
    } elseif (!empty($idVal)) {
        $orderObj = $orderModel->getOrderByRef($idVal);
        if ($orderObj) {
            $numericId = (int)$orderObj['id'];
        }
    }

    if ($method === 'GET') {
        if ($action === 'update_status' || in_array($action, ['Pending', 'Shipped', 'Delivered', 'Cancelled'])) {
            $orderController->handleUpdateStatus($_GET);
        } elseif (is_numeric($idVal) && (int)$idVal > 0) {
            $orderController->handleGetOrderById((int)$idVal);
        } elseif (!empty($idVal)) {
            $order = $orderModel->getOrderByRef($idVal);
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
        if ($numericId > 0) {
            $orderController->handleUpdateOrder($numericId, $requestData);
        } else {
            jsonResponse(['status' => 'error', 'message' => 'Bad Request: Missing or invalid order ID.'], 400);
        }
    } elseif ($method === 'DELETE') {
        if ($numericId > 0) {
            $orderController->handleDeleteOrder($numericId);
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
