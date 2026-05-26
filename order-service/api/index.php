<?php
header('Content-Type: application/json');
echo json_encode([
    'service' => 'order-service',
    'status' => 'ok',
    'message' => 'Starter endpoint is running.'
]);
