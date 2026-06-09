<?php
/**
 * catalog-service/api/index.php
 * Catalog Service entry point.
 */
require_once __DIR__ . '/../../shared/utils/response.php';

jsonResponse([
    'service' => 'catalog-service',
    'status' => 'ok',
    'endpoints' => [
        'books.php'
    ]
], 200);
