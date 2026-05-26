<?php
header('Content-Type: application/json');
echo json_encode([
    'service' => 'notification-service',
    'status' => 'ok',
    'message' => 'Starter endpoint is running.'
]);
