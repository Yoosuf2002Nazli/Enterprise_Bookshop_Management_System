<?php
/**
 * catalog-service/api/books.php
 * Entry point for catalog search and filtering requests.
 */

// 1. Import dependencies
require_once __DIR__ . '/../../shared/database/connection_template.php';
require_once __DIR__ . '/../../shared/utils/response.php';
require_once __DIR__ . '/../models/BookModel.php';
require_once __DIR__ . '/../controllers/BookController.php';

// 2. Enforce GET request method
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse([
        'status' => 'error',
        'message' => 'Method Not Allowed. Only GET requests are supported.'
    ], 405);
    return;
}

// Load configurations
$dbConfig = require __DIR__ . '/../config/config.php';

try {
    // 3. Connect to database and process request
    $pdo = createPdoConnection($dbConfig);
    $bookModel = new BookModel($pdo);
    $bookController = new BookController($bookModel);

    $bookController->handleGetBooks($_GET);

} catch (Exception $e) {
    jsonResponse([
        'status' => 'error',
        'message' => 'Internal Server Error: ' . $e->getMessage()
    ], 500);
}
