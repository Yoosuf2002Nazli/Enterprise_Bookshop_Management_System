<?php
header('Content-Type: application/json');
echo json_encode([
    'service' => 'user-service',
    'status' => 'ok',
    'message' => 'Starter endpoint is running.'
]);
