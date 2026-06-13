<?php
// /frontend/components/config.php
/**
 * Frontend Configuration & Dynamic Path Resolver
 * Ensures relative path imports and URLs function seamlessly on XAMPP subfolders or custom domains.
 */

// Start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Calculate the frontend folder path relative to the server host domain
// Handles environments like:
// - http://localhost/bookshop-management-system/frontend/index.php
// - http://localhost/Enterprise_Bookshop_Management_System/frontend/index.php
// - http://localhost/frontend/index.php
// - http://bookshop.test/index.php
$script_name = $_SERVER['SCRIPT_NAME']; // e.g. "/bookshop-management-system/frontend/index.php"
$frontend_pos = strpos($script_name, '/frontend/');

if ($frontend_pos !== false) {
    // Extract base URL path up to and including the '/frontend/' segment
    $base_url = substr($script_name, 0, $frontend_pos + 10);
} else {
    // Fallback if hosted directly at the root of a domain
    $base_url = '/';
}

// Helper utility to safely output strings to prevent XSS (Cross-Site Scripting)
if (!function_exists('escape')) {
    function escape($string) {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }
}

// Service URL Constants
define('USER_SERVICE_URL', 'http://localhost:8001/api/auth.php');
define('CATALOG_SERVICE_URL', 'http://localhost:8002/api/books.php');
define('INVENTORY_SERVICE_URL', 'http://localhost:8003/api/inventory.php');
define('ORDER_SERVICE_URL', 'http://localhost:8004/api/orders.php');
define('NOTIFICATION_SERVICE_URL', 'http://localhost:8005/api/notify.php');

/**
 * Helper to call a microservice API via HTTP (GET/POST/PUT/DELETE)
 */
if (!function_exists('makeServiceRequest')) {
    function makeServiceRequest(string $url, string $method = 'GET', ?array $data = null): ?array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        
        if ($method === 'GET' && !empty($data)) {
            $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($data);
        }
        
        curl_setopt($ch, CURLOPT_URL, $url);
        
        if (in_array($method, ['POST', 'PUT', 'DELETE']) && $data !== null) {
            $jsonData = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonData)
            ]);
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($response === false) {
            return null;
        }
        
        return json_decode($response, true);
    }
}
?>
