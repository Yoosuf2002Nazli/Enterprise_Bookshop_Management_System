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

    // Route request depending on HTTP verb and action query parameters
    if ($action === 'restock' && $method === 'GET') {
        $inventoryController->handleRestock($_GET);
    } elseif ($action === 'reduce' && $method === 'POST') {
        $inventoryController->handleReduceStock($_POST);
    } elseif (empty($action) && $method === 'GET') {
        $inventoryController->handleGetInventory($_GET);
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
