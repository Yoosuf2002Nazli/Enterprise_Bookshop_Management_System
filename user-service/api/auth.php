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

    // 4. Resolve the route action
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'register':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(['status' => 'error', 'message' => 'Method Not Allowed'], 405);
                return;
            }
            $authController->handleRegister($_POST);
            break;

        case 'login':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(['status' => 'error', 'message' => 'Method Not Allowed'], 405);
                return;
            }
            $authController->handleLogin($_POST);
            break;

        case 'logout':
            // Logout action is valid for both GET and POST requests
            $authController->handleLogout();
            break;

        default:
            jsonResponse(['status' => 'error', 'message' => 'Bad Request: Action parameter is invalid or missing.'], 400);
            break;
    }

} catch (Exception $e) {
    // Respond with server error if connection or execution throws an exception
    jsonResponse([
        'status' => 'error',
        'message' => 'Internal Server Error: ' . $e->getMessage()
    ], 500);
}
