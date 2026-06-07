<?php
// inventory-service/api/stock.php
/**
 * Inventory Service - Stock API Endpoint
 * RESTful API for stock level management and transactions
 * 
 * Endpoints:
 * GET  /inventory-service/api/stock.php                                - List all stock levels
 * GET  /inventory-service/api/stock.php?book_id=1&warehouse_id=1       - Get specific stock
 * GET  /inventory-service/api/stock.php?low_stock=true                 - Get low stock items
 * GET  /inventory-service/api/stock.php?out_of_stock=true              - Get out of stock
 * GET  /inventory-service/api/stock.php?warehouse_summary=true         - Get warehouse summary
 * POST /inventory-service/api/stock.php?action=add                     - Add stock
 * POST /inventory-service/api/stock.php?action=remove                  - Remove stock
 * POST /inventory-service/api/stock.php?action=adjust                  - Adjust stock level
 * PUT  /inventory-service/api/stock.php?id=1                           - Update reorder point
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$config = require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../../shared/database/connection_template.php';
require_once __DIR__ . '/../../shared/utils/response.php';
require_once __DIR__ . '/../models/StockLevel.php';
require_once __DIR__ . '/../models/StockTransaction.php';

try {
    $pdo = createPdoConnection($config);
    $stockModel = new StockLevel($pdo);
    $transactionModel = new StockTransaction($pdo);

    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? null;

    switch ($method) {
        case 'GET':
            handleStockGet($stockModel, $transactionModel);
            break;
        case 'POST':
            handleStockPost($stockModel, $transactionModel);
            break;
        case 'PUT':
            handleStockPut($stockModel);
            break;
        default:
            jsonResponse(['error' => 'Method not allowed'], 405);
    }

} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}

/**
 * Handle GET requests for inventory data
 */
function handleStockGet($stockModel, $transactionModel) {
    try {
        $bookId = $_GET['book_id'] ?? null;
        $warehouseId = $_GET['warehouse_id'] ?? null;
        $lowStock = isset($_GET['low_stock']);
        $outOfStock = isset($_GET['out_of_stock']);
        $warehouseSummary = isset($_GET['warehouse_summary']);

        if ($warehouseSummary) {
            // Get warehouse-level summary
            $summary = $stockModel->getWarehouseSummary();
            jsonResponse([
                'success' => true,
                'data' => $summary,
                'type' => 'warehouse_summary'
            ]);

        } elseif ($lowStock) {
            // Get items below reorder point
            $items = $stockModel->getLowStockItems();
            jsonResponse([
                'success' => true,
                'data' => $items,
                'type' => 'low_stock',
                'count' => count($items)
            ]);

        } elseif ($outOfStock) {
            // Get items with zero quantity
            $items = $stockModel->getOutOfStockItems();
            jsonResponse([
                'success' => true,
                'data' => $items,
                'type' => 'out_of_stock',
                'count' => count($items)
            ]);

        } elseif ($bookId && $warehouseId) {
            // Get specific stock level
            $stock = $stockModel->getByBookAndWarehouse($bookId, $warehouseId);
            if (!$stock) {
                jsonResponse(['error' => 'Stock record not found'], 404);
                return;
            }
            jsonResponse(['success' => true, 'data' => $stock]);

        } else {
            // List all stock levels with optional filters
            $filters = [];
            if ($warehouseId) $filters['warehouse_id'] = $warehouseId;
            if ($lowStock) $filters['low_stock'] = true;
            if ($outOfStock) $filters['out_of_stock'] = true;

            $stocks = $stockModel->getAll($filters);
            jsonResponse([
                'success' => true,
                'data' => $stocks,
                'count' => count($stocks)
            ]);
        }

    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 400);
    }
}

/**
 * Handle POST requests (Stock operations)
 */
function handleStockPost($stockModel, $transactionModel) {
    $action = $_GET['action'] ?? null;
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    if (!isset($input['book_id']) || !isset($input['warehouse_id'])) {
        jsonResponse(['error' => 'book_id and warehouse_id are required'], 400);
        return;
    }

    try {
        $bookId = $input['book_id'];
        $warehouseId = $input['warehouse_id'];
        $quantity = (int)($input['quantity'] ?? 0);

        $stock = $stockModel->getByBookAndWarehouse($bookId, $warehouseId);
        if (!$stock) {
            jsonResponse(['error' => 'Stock record not found'], 404);
            return;
        }

        $success = false;
        $transactionType = '';
        $message = '';

        switch ($action) {
            case 'add':
                // Add stock (Inbound/Restock)
                $success = $stockModel->addStock($bookId, $warehouseId, $quantity);
                $transactionType = 'IN';
                $message = "Added $quantity units to stock";
                if ($success) {
                    $stockModel->recordRestock($bookId, $warehouseId);
                }
                break;

            case 'remove':
                // Remove stock (Outbound/Sale)
                $success = $stockModel->removeStock($bookId, $warehouseId, $quantity);
                $transactionType = 'OUT';
                $message = $success ? "Removed $quantity units from stock" : "Insufficient stock";
                break;

            case 'adjust':
                // Direct adjustment
                $success = $stockModel->updateQuantity($bookId, $warehouseId, $quantity);
                $transactionType = 'ADJUSTMENT';
                $message = "Adjusted stock to $quantity units";
                break;

            default:
                jsonResponse(['error' => 'Invalid action. Use: add, remove, adjust'], 400);
                return;
        }

        if (!$success) {
            jsonResponse(['error' => 'Failed to update stock'], 400);
            return;
        }

        // Log transaction
        $transactionModel->create([
            'book_id' => $bookId,
            'warehouse_id' => $warehouseId,
            'transaction_type' => $transactionType,
            'quantity' => $quantity,
            'reason' => $input['reason'] ?? null,
            'reference_id' => $input['reference_id'] ?? null,
            'notes' => $input['notes'] ?? null
        ]);

        // Get updated stock
        $updatedStock = $stockModel->getByBookAndWarehouse($bookId, $warehouseId);

        jsonResponse([
            'success' => true,
            'message' => $message,
            'action' => $action,
            'data' => $updatedStock
        ]);

    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 400);
    }
}

/**
 * Handle PUT requests (Update configuration)
 */
function handleStockPut($stockModel) {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['book_id']) || !isset($input['warehouse_id'])) {
        jsonResponse(['error' => 'book_id and warehouse_id are required'], 400);
        return;
    }

    try {
        $bookId = $input['book_id'];
        $warehouseId = $input['warehouse_id'];

        $stock = $stockModel->getByBookAndWarehouse($bookId, $warehouseId);
        if (!$stock) {
            jsonResponse(['error' => 'Stock record not found'], 404);
            return;
        }

        // Update reorder configuration
        if (isset($input['reorder_point']) && isset($input['reorder_quantity'])) {
            $success = $stockModel->updateReorderPoint(
                $bookId,
                $warehouseId,
                $input['reorder_point'],
                $input['reorder_quantity']
            );

            if (!$success) {
                jsonResponse(['error' => 'Failed to update reorder point'], 400);
                return;
            }
        }

        $updatedStock = $stockModel->getByBookAndWarehouse($bookId, $warehouseId);

        jsonResponse([
            'success' => true,
            'message' => 'Stock configuration updated',
            'data' => $updatedStock
        ]);

    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 400);
    }
}