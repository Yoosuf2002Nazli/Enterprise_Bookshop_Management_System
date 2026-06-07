<?php
/**
 * inventory-service/api/index.php
 * Inventory Service entry point.
 */
require_once __DIR__ . '/../../shared/utils/response.php';

jsonResponse([
    'service' => 'inventory-service',
    'status' => 'ok',
    'endpoints' => [
        'inventory.php'
    ]
], 200);
