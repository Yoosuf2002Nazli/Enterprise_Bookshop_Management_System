<?php
/**
 * inventory-service/api/inventory.php
 * Entry point for inventory stock queries, replenishments, and reductions.
 */

// 1. Import dependencies
require_once __DIR__ . '/../../shared/database/connection_template.php';
require_once __DIR__ . '/../../shared/utils/response.php';
require_once __DIR__ . '/../models/InventoryModel.php';
require_once __DIR__ . '/../controllers/InventoryController.php';

// Load configurations
$dbConfig = require __DIR__ . '/../config/config.php';

try {
    // 2. Connect to database and process request
    $pdo = createPdoConnection($dbConfig);
    $inventoryModel = new InventoryModel($pdo);
    $inventoryController = new InventoryController($inventoryModel);

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
        if ($action === 'restock') {
            $inventoryController->handleRestock($_GET);
        } elseif ($id > 0) {
            $inventoryController->handleGetInventoryById($id);
        } else {
            $inventoryController->handleGetInventory($_GET);
        }
    } elseif ($method === 'POST') {
        if ($action === 'reduce') {
            $inventoryController->handleReduceStock($requestData);
        } elseif ($action === 'restock') {
            $inventoryController->handleRestock($requestData);
        } else {
            $inventoryController->handleCreateInventory($requestData);
        }
    } elseif ($method === 'PUT') {
        if ($action === 'restock') {
            $inventoryController->handleRestock(array_merge($_GET, $requestData));
        } elseif ($id > 0) {
            $inventoryController->handleUpdateInventory($id, $requestData);
        } else {
            jsonResponse(['status' => 'error', 'message' => 'Bad Request: Missing or invalid inventory ID.'], 400);
        }
    } elseif ($method === 'DELETE') {
        if ($id <= 0) {
            jsonResponse(['status' => 'error', 'message' => 'Bad Request: Missing or invalid inventory ID.'], 400);
        } else {
            $inventoryController->handleDeleteInventory($id);
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
