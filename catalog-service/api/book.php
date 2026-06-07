<?php
// catalog-service/api/books.php
/**
 * Catalog Service - Books API Endpoint
 * RESTful API for book catalog operations
 * 
 * Endpoints:
 * GET  /catalog-service/api/books.php                    - List all books
 * GET  /catalog-service/api/books.php?id=1               - Get single book
 * GET  /catalog-service/api/books.php?category=1         - List by category
 * GET  /catalog-service/api/books.php?search=query       - Search books
 * POST /catalog-service/api/books.php                    - Create book (admin)
 * PUT  /catalog-service/api/books.php?id=1               - Update book (admin)
 * DELETE /catalog-service/api/books.php?id=1             - Delete book (admin)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Load configuration and models
$config = require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../../shared/database/connection_template.php';
require_once __DIR__ . '/../../shared/utils/response.php';
require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../models/Category.php';

try {
    // Create database connection
    $pdo = createPdoConnection($config);
    $bookModel = new Book($pdo);
    $categoryModel = new Category($pdo);

    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? null;

    // Route handling
    switch ($method) {
        case 'GET':
            handleGet($bookModel, $categoryModel);
            break;
        case 'POST':
            handlePost($bookModel);
            break;
        case 'PUT':
            handlePut($bookModel);
            break;
        case 'DELETE':
            handleDelete($bookModel);
            break;
        default:
            jsonResponse(['error' => 'Method not allowed'], 405);
    }

} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}

/**
 * Handle GET requests
 */
/*function handleGet($bookModel, $categoryModel) {
    $id = $_GET['id'] ?? null;
    $categoryId = $_GET['category'] ?? null;
    $search = $_GET['search'] ?? null;
    $page = (int)($_GET['page'] ?? 1);
    $perPage = (int)($_GET['per_page'] ?? 10);

    try {
        if ($id) {
            // Get single book
            $book = $bookModel->getById($id);
            if (!$book) {
                jsonResponse(['error' => 'Book not found'], 404);
                return;
            }
            jsonResponse(['success' => true, 'data' => $book]);

        } elseif ($categoryId) {
            // Get books by category
            $books = $bookModel->getByCategory($categoryId);
            $category = $categoryModel->getById($categoryId);
            jsonResponse([
                'success' => true,
                'category' => $category,
                'data' => $books,
                'count' => count($books)
            ]);

        } elseif ($search) {
            // Search books
            $results = $bookModel->search($search);
            jsonResponse([
                'success' => true,
                'query' => $search,
                'data' => $results,
                'count' => count($results)
            ]);

        } else {
            // List all books with pagination
            $paginatedResult = $bookModel->getPaginated($page, $perPage);
            jsonResponse([
                'success' => true,
                'data' => $paginatedResult['data'],
                'pagination' => [
                    'current_page' => $paginatedResult['page'],
                    'per_page' => $paginatedResult['per_page'],
                    'total' => $paginatedResult['total'],
                    'total_pages' => $paginatedResult['pages']
                ]
            ]);
        }
    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 400);
    }
}**/
/**
 * Handle GET requests
 */
function handleGet($bookModel, $categoryModel) {
    $id = $_GET['id'] ?? null;
    $categoryId = $_GET['category'] ?? null;
    $search = $_GET['search'] ?? null;
    
    // Check if pagination variables are coming from a frontend component, otherwise fetch a large batch for catalog browse
    $page = isset($_GET['page']) ? (int)$_GET['page'] : null;
    $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 100; // Large ceiling fallback

    try {
        if ($id) {
            // Get single book
            $book = $bookModel->getById($id);
            if (!$book) {
                jsonResponse(['error' => 'Book not found'], 404);
                return;
            }
            // Inject UI keys for single book render just in case
            $book = injectUiMetas($book);
            jsonResponse(['success' => true, 'data' => $book]);

        } elseif ($categoryId) {
            // Get books by category
            $books = $bookModel->getByCategory($categoryId);
            $category = $categoryModel->getById($categoryId);
            
            foreach ($books as &$b) { $b = injectUiMetas($b); }
            
            jsonResponse([
                'success' => true,
                'category' => $category,
                'data' => $books,
                'count' => count($books)
            ]);

        } elseif ($search) {
            // Search books
            $results = $bookModel->search($search);
            
            foreach ($results as &$b) { $b = injectUiMetas($b); }
            
            jsonResponse([
                'success' => true,
                'query' => $search,
                'data' => $results,
                'count' => count($results)
            ]);

        } else {
            // List books - check if layout expects flat catalog array or clean structure
            if (isset($_GET['page'])) {
                $paginatedResult = $bookModel->getPaginated($page, $perPage);
                $books = $paginatedResult['data'];
                
                foreach ($books as &$b) { $b = injectUiMetas($b); }
                
                jsonResponse([
                    'success' => true,
                    'data' => $books,
                    'pagination' => [
                        'current_page' => $paginatedResult['page'],
                        'per_page' => $paginatedResult['per_page'],
                        'total' => $paginatedResult['total'],
                        'total_pages' => $paginatedResult['pages']
                    ]
                ]);
            } else {
                // FALLBACK FOR FRONTEND: If frontend hits raw endpoint, provide data collection safely
                // Change 'getAll()' to match whatever retrieval method your Book model uses for non-paginated lists
                $books = method_exists($bookModel, 'getAll') ? $bookModel->getAll() : $bookModel->getPaginated(1, 100)['data'];
                
                foreach ($books as &$b) { $b = injectUiMetas($b); }
                
                jsonResponse([
                    'success' => true,
                    'data' => $books
                ]);
            }
        }
    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 400);
    }
}

/**
 * Helper utility to inject Bootstrap structural metadata icons into rows 
 * missing them from SQL Server schemas
 */
function injectUiMetas($book) {
    if (!isset($book['icon']) || empty($book['icon'])) {
        // Check both 'category' and 'category_name' fields for backward compatibility
        $category = strtolower($book['category_name'] ?? $book['category'] ?? 'all');
        switch ($category) {
            case 'technology':
            case 'programming':
                $book['icon'] = 'bi-code-square';
                $book['icon_color'] = 'text-primary';
                break;
            case 'fiction':
                $book['icon'] = 'bi-compass-fill';
                $book['icon_color'] = 'text-warning';
                break;
            case 'business':
                $book['icon'] = 'bi-graph-up-arrow';
                $book['icon_color'] = 'text-success';
                break;
            case 'science':
                $book['icon'] = 'bi-stars';
                $book['icon_color'] = 'text-info';
                break;
            default:
                $book['icon'] = 'bi-journal-text';
                $book['icon_color'] = 'text-secondary';
                break;
        }
    }
    return $book;
}

/**
 * Handle POST requests (Create)
 */
function handlePost($bookModel) {
    // Validate authentication (basic check - implement proper auth later)
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
    if (!$authHeader || strpos($authHeader, 'Bearer') === false) {
        jsonResponse(['error' => 'Unauthorized - Admin token required'], 401);
        return;
    }

    // Get JSON body
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    $required = ['title', 'author', 'isbn', 'price'];
    foreach ($required as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            jsonResponse(['error' => "Missing required field: $field"], 400);
            return;
        }
    }

    try {
        $bookId = $bookModel->create($input);
        if (!$bookId) {
            jsonResponse(['error' => 'Failed to create book'], 400);
            return;
        }

        $newBook = $bookModel->getById($bookId);
        jsonResponse([
            'success' => true,
            'message' => 'Book created successfully',
            'data' => $newBook
        ], 201);

    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 400);
    }
}

/**
 * Handle PUT requests (Update)
 */
function handlePut($bookModel) {
    // Validate authentication
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
    if (!$authHeader || strpos($authHeader, 'Bearer') === false) {
        jsonResponse(['error' => 'Unauthorized - Admin token required'], 401);
        return;
    }

    $id = $_GET['id'] ?? null;
    if (!$id) {
        jsonResponse(['error' => 'Book ID is required'], 400);
        return;
    }

    // Get JSON body
    $input = json_decode(file_get_contents('php://input'), true);

    try {
        $book = $bookModel->getById($id);
        if (!$book) {
            jsonResponse(['error' => 'Book not found'], 404);
            return;
        }

        $success = $bookModel->update($id, $input);
        if (!$success) {
            jsonResponse(['error' => 'Failed to update book'], 400);
            return;
        }

        $updatedBook = $bookModel->getById($id);
        jsonResponse([
            'success' => true,
            'message' => 'Book updated successfully',
            'data' => $updatedBook
        ]);

    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 400);
    }
}

/**
 * Handle DELETE requests
 */
function handleDelete($bookModel) {
    // Validate authentication
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
    if (!$authHeader || strpos($authHeader, 'Bearer') === false) {
        jsonResponse(['error' => 'Unauthorized - Admin token required'], 401);
        return;
    }

    $id = $_GET['id'] ?? null;
    if (!$id) {
        jsonResponse(['error' => 'Book ID is required'], 400);
        return;
    }

    try {
        $book = $bookModel->getById($id);
        if (!$book) {
            jsonResponse(['error' => 'Book not found'], 404);
            return;
        }

        $success = $bookModel->delete($id);
        jsonResponse([
            'success' => true,
            'message' => 'Book deleted successfully',
            'deleted_id' => $id
        ]);

    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 400);
    }
}