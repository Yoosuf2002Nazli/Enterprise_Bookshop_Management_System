<?php
/**
 * notification-service/api/index.php
 * Notification Service entry point.
 */
require_once __DIR__ . '/../../shared/utils/response.php';

jsonResponse([
    'service' => 'notification-service',
    'status' => 'ok',
    'endpoints' => [
        'notify.php'
    ]
], 200);
