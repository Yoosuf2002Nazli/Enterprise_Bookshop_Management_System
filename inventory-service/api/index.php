<?php
header('Content-Type: application/json');
echo json_encode([
    'service' => 'inventory-service',
    'status' => 'ok',
    'message' => 'Starter endpoint is running.'
]);
