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

// 2. Resolve request parameters and HTTP method
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Parse request body for POST/PUT/DELETE JSON inputs
$requestData = $_POST;
$rawBody = file_get_contents('php://input');
if (!empty($rawBody)) {
    $jsonBody = json_decode($rawBody, true);
    if (is_array($jsonBody)) {
        $requestData = array_merge($requestData, $jsonBody);
    }
}

// Load configurations
$dbConfig = require __DIR__ . '/../config/config.php';

try {
    // 3. Connect to database and process request
    $pdo = createPdoConnection($dbConfig);
    $bookModel = new BookModel($pdo);
    $bookController = new BookController($bookModel);

    if ($method === 'GET') {
        if ($id > 0) {
            $bookController->handleGetBookById($id);
        } else {
            $bookController->handleGetBooks($_GET);
        }
    } elseif ($method === 'POST') {
        $bookController->handleCreateBook($requestData);
    } elseif ($method === 'PUT') {
        if ($id <= 0) {
            jsonResponse(['status' => 'error', 'message' => 'Bad Request: Missing or invalid book ID.'], 400);
        } else {
            $bookController->handleUpdateBook($id, $requestData);
        }
    } elseif ($method === 'DELETE') {
        if ($id <= 0) {
            jsonResponse(['status' => 'error', 'message' => 'Bad Request: Missing or invalid book ID.'], 400);
        } else {
            $bookController->handleDeleteBook($id);
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
