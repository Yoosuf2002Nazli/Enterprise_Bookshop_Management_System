<?php
// catalog-service/api/categories.php
/**
 * Catalog Service - Categories API Endpoint
 * RESTful API for category management
 * 
 * Endpoints:
 * GET  /catalog-service/api/categories.php              - List all categories
 * GET  /catalog-service/api/categories.php?id=1         - Get single category
 * GET  /catalog-service/api/categories.php?with_counts  - List with book counts
 * POST /catalog-service/api/categories.php              - Create category
 * PUT  /catalog-service/api/categories.php?id=1         - Update category
 * DELETE /catalog-service/api/categories.php?id=1       - Delete category
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
require_once __DIR__ . '/../models/Category.php';

try {
    $pdo = createPdoConnection($config);
    $categoryModel = new Category($pdo);

    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
        case 'GET':
            handleCategoryGet($categoryModel);
            break;
        case 'POST':
            handleCategoryPost($categoryModel);
            break;
        case 'PUT':
            handleCategoryPut($categoryModel);
            break;
        case 'DELETE':
            handleCategoryDelete($categoryModel);
            break;
        default:
            jsonResponse(['error' => 'Method not allowed'], 405);
    }

} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}

function handleCategoryGet($categoryModel) {
    $id = $_GET['id'] ?? null;
    $withCounts = isset($_GET['with_counts']);
    $slug = $_GET['slug'] ?? null;

    try {
        if ($id) {
            $category = $withCounts 
                ? $categoryModel->getWithBookCount($id)
                : $categoryModel->getById($id);
            
            if (!$category) {
                jsonResponse(['error' => 'Category not found'], 404);
                return;
            }
            jsonResponse(['success' => true, 'data' => $category]);

        } elseif ($slug) {
            $category = $categoryModel->getBySlug($slug);
            if (!$category) {
                jsonResponse(['error' => 'Category not found'], 404);
                return;
            }
            jsonResponse(['success' => true, 'data' => $category]);

        } else {
            $categories = $withCounts 
                ? $categoryModel->getAllWithCounts()
                : $categoryModel->getAll();
            
            jsonResponse([
                'success' => true,
                'data' => $categories,
                'count' => count($categories)
            ]);
        }
    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 400);
    }
}

function handleCategoryPost($categoryModel) {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
    if (!$authHeader || strpos($authHeader, 'Bearer') === false) {
        jsonResponse(['error' => 'Unauthorized'], 401);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['name']) || empty($input['name'])) {
        jsonResponse(['error' => 'Category name is required'], 400);
        return;
    }

    try {
        $categoryId = $categoryModel->create($input);
        if (!$categoryId) {
            jsonResponse(['error' => 'Failed to create category'], 400);
            return;
        }

        $newCategory = $categoryModel->getById($categoryId);
        jsonResponse([
            'success' => true,
            'message' => 'Category created',
            'data' => $newCategory
        ], 201);

    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 400);
    }
}

function handleCategoryPut($categoryModel) {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
    if (!$authHeader || strpos($authHeader, 'Bearer') === false) {
        jsonResponse(['error' => 'Unauthorized'], 401);
        return;
    }

    $id = $_GET['id'] ?? null;
    if (!$id) {
        jsonResponse(['error' => 'Category ID required'], 400);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    try {
        $category = $categoryModel->getById($id);
        if (!$category) {
            jsonResponse(['error' => 'Category not found'], 404);
            return;
        }

        $success = $categoryModel->update($id, $input);
        if (!$success) {
            jsonResponse(['error' => 'Failed to update category'], 400);
            return;
        }

        $updatedCategory = $categoryModel->getById($id);
        jsonResponse([
            'success' => true,
            'message' => 'Category updated',
            'data' => $updatedCategory
        ]);

    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 400);
    }
}

function handleCategoryDelete($categoryModel) {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
    if (!$authHeader || strpos($authHeader, 'Bearer') === false) {
        jsonResponse(['error' => 'Unauthorized'], 401);
        return;
    }

    $id = $_GET['id'] ?? null;
    if (!$id) {
        jsonResponse(['error' => 'Category ID required'], 400);
        return;
    }

    try {
        $category = $categoryModel->getById($id);
        if (!$category) {
            jsonResponse(['error' => 'Category not found'], 404);
            return;
        }

        $success = $categoryModel->delete($id);
        jsonResponse([
            'success' => true,
            'message' => 'Category deleted',
            'deleted_id' => $id
        ]);

    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 400);
    }
}