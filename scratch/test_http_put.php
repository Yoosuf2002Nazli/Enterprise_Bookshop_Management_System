<?php
// scratch/test_http_put.php

$url = 'http://localhost:8002/api/books.php?id=8';
$data = [
    'title' => 'API Test Book Updated',
    'author' => 'Test Author Updated',
    'isbn' => '978-0000000002',
    'category' => 'Technology',
    'price' => 49.99
];

$options = [
    'http' => [
        'header'  => "Content-Type: application/json\r\n",
        'method'  => 'PUT',
        'content' => json_encode($data),
        'ignore_errors' => true
    ]
];

$context  = stream_context_create($options);
$response = file_get_contents($url, false, $context);

echo "HTTP Response Headers:\n";
print_r($http_response_header);
echo "\nHTTP Response Body:\n";
echo $response . "\n";
