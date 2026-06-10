<?php
/**
 * user-service/api/auth.php
 * Entry point for authentication routing (register, login, logout).
 */

// 1. Initialize session safely if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Import global utilities and configurations
require_once __DIR__ . '/../../shared/database/connection_template.php';
require_once __DIR__ . '/../../shared/utils/response.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../controllers/AuthController.php';

// Load service configurations
$dbConfig = require __DIR__ . '/../config/config.php';

try {
    // 3. Connect to the user database
    $pdo = createPdoConnection($dbConfig);
    $userModel = new UserModel($pdo);
    $authController = new AuthController($userModel);

    // 4. Parse raw request body (JSON or Form-data)
    $requestData = $_POST;
    $rawBody = file_get_contents('php://input');
    if (!empty($rawBody)) {
        $jsonBody = json_decode($rawBody, true);
        if (is_array($jsonBody)) {
            $requestData = array_merge($requestData, $jsonBody);
        }
    }

    $action = $_GET['action'] ?? '';
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        if ($action === 'logout') {
            $authController->handleLogout();
        } elseif ($id > 0) {
            $authController->handleGetUserById($id);
        } else {
            if (in_array($action, ['register', 'login'])) {
                jsonResponse(['status' => 'error', 'message' => 'Method Not Allowed'], 405);
            } else {
                $authController->handleGetUsers();
            }
        }
    } elseif ($method === 'POST') {
        if ($action === 'register') {
            $authController->handleRegister($requestData);
        } elseif ($action === 'login') {
            $authController->handleLogin($requestData);
        } elseif ($action === 'logout') {
            $authController->handleLogout();
        } else {
            // Default POST can be register
            $authController->handleRegister($requestData);
        }
    } elseif ($method === 'PUT') {
        if ($id <= 0) {
            jsonResponse(['status' => 'error', 'message' => 'Bad Request: Missing or invalid user ID.'], 400);
        } else {
            $authController->handleUpdateUser($id, $requestData);
        }
    } elseif ($method === 'DELETE') {
        if ($id <= 0) {
            jsonResponse(['status' => 'error', 'message' => 'Bad Request: Missing or invalid user ID.'], 400);
        } else {
            $authController->handleDeleteUser($id);
        }
    } else {
        jsonResponse(['status' => 'error', 'message' => 'Method Not Allowed'], 405);
    }

} catch (Exception $e) {
    // Respond with server error if connection or execution throws an exception
    jsonResponse([
        'status' => 'error',
        'message' => 'Internal Server Error: ' . $e->getMessage()
    ], 500);
}
