<?php
/**
 * user-service/api/index.php
 * Listing user service metadata and API routes.
 */
require_once __DIR__ . '/../../shared/utils/response.php';

jsonResponse([
    'service' => 'user-service',
    'status' => 'ok',
    'endpoints' => [
        'auth.php?action=register',
        'auth.php?action=login',
        'auth.php?action=logout'
    ]
], 200);
