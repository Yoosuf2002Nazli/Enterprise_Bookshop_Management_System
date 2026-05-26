<?php
header('Content-Type: application/json');
echo json_encode([
    'service' => 'catalog-service',
    'status' => 'ok',
    'message' => 'Starter endpoint is running.'
]);
