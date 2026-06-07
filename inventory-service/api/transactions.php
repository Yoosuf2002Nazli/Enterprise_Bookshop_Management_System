<?php
// inventory-service/api/transactions.php
/**
 * Inventory Service - Transactions API Endpoint
 * RESTful API for viewing stock transaction audit trail
 * 
 * Endpoints:
 * GET  /inventory-service/api/transactions.php                         - List all transactions
 * GET  /inventory-service/api/transactions.php?book_id=1               - Transactions for book
 * GET  /inventory-service/api/transactions.php?warehouse_id=1          - Transactions for warehouse
 * GET  /inventory-service/api/transactions.php?type=IN                 - Filter by type
 * GET  /inventory-service/api/transactions.php?summary=true            - Get summary by type
 * GET  /inventory-service/api/transactions.php?id=1                    - Get single transaction
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    jsonResponse(['error' => 'Method not allowed'], 405);
    exit;
}

$config = require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../../shared/database/connection_template.php';
require_once __DIR__ . '/../../shared/utils/response.php';
require_once __DIR__ . '/../models/StockTransaction.php';

try {
    $pdo = createPdoConnection($config);
    $transactionModel = new StockTransaction($pdo);

    $id = $_GET['id'] ?? null;
    $bookId = $_GET['book_id'] ?? null;
    $warehouseId = $_GET['warehouse_id'] ?? null;
    $type = $_GET['type'] ?? null;
    $summary = isset($_GET['summary']);
    $startDate = $_GET['start_date'] ?? null;
    $endDate = $_GET['end_date'] ?? null;

    if ($id) {
        // Get single transaction
        $transaction = $transactionModel->getById($id);
        if (!$transaction) {
            jsonResponse(['error' => 'Transaction not found'], 404);
            exit;
        }
        jsonResponse(['success' => true, 'data' => $transaction]);

    } elseif ($bookId) {
        // Get transactions for specific book
        $transactions = $transactionModel->getByBook($bookId);
        jsonResponse([
            'success' => true,
            'book_id' => $bookId,
            'data' => $transactions,
            'count' => count($transactions)
        ]);

    } elseif ($warehouseId) {
        // Get transactions for specific warehouse
        $transactions = $transactionModel->getByWarehouse($warehouseId);
        jsonResponse([
            'success' => true,
            'warehouse_id' => $warehouseId,
            'data' => $transactions,
            'count' => count($transactions)
        ]);

    } elseif ($summary) {
        // Get summary by transaction type
        $summaryData = $transactionModel->getSummaryByType($warehouseId);
        jsonResponse([
            'success' => true,
            'type' => 'summary_by_type',
            'data' => $summaryData
        ]);

    } else {
        // List all transactions with optional filters
        $filters = [];
        if ($type) $filters['type'] = $type;
        if ($startDate) $filters['start_date'] = $startDate;
        if ($endDate) $filters['end_date'] = $endDate;
        if ($bookId) $filters['book_id'] = $bookId;
        if ($warehouseId) $filters['warehouse_id'] = $warehouseId;

        $transactions = $transactionModel->getAll($filters);
        jsonResponse([
            'success' => true,
            'data' => $transactions,
            'count' => count($transactions),
            'filters' => $filters
        ]);
    }

} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}