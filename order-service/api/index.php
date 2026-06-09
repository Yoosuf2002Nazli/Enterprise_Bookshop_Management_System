<?php
/**
 * order-service/api/index.php
 * Order Service entry point.
 */
require_once __DIR__ . '/../../shared/utils/response.php';

jsonResponse([
    'service' => 'order-service',
    'status' => 'ok',
    'endpoints' => [
        'orders.php'
    ]
], 200);
