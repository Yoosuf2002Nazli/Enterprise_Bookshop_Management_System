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
?>
