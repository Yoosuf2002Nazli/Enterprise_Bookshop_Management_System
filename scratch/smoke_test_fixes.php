<?php
// scratch/smoke_test_fixes.php

function makeHttpRequest($url, $method, $data = null) {
    $options = [
        'http' => [
            'header'  => "Content-Type: application/json\r\n",
            'method'  => $method,
            'ignore_errors' => true
        ]
    ];
    if ($data !== null) {
        $options['http']['content'] = json_encode($data);
    }
    
    $context  = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    
    return [
        'status' => $http_response_header[0] ?? 'Unknown',
        'body'   => $response
    ];
}

echo "===========================================\n";
echo "  MICROSERVICES SMOKE RETESTS FOR FIXES\n";
echo "===========================================\n\n";

// Test 1: Catalog PUT Update on book ID 4 (with minimal valid JSON body)
echo "--- Test 1: PUT Update Book ID 4 ---\n";
$putBookData = [
    "title" => "API Test Book Updated",
    "author" => "Test Author Updated",
    "isbn" => "978-0307887894", // ID 4's original ISBN to avoid duplicate key error
    "category" => "Technology",
    "price" => 49.99
];
$res = makeHttpRequest("http://localhost:8002/api/books.php?id=4", "PUT", $putBookData);
echo "Status: " . $res['status'] . "\n";
echo "Body: " . $res['body'] . "\n\n";

// Test 2: Order POST Create Order (independent API testing with decoupled inventory reduction)
echo "--- Test 2: POST Create Order ---\n";
$postOrderData = [
    "customer" => "API Test Order Customer 003",
    "email" => "apitestorder003@university.edu",
    "book_id" => 3,
    "book_title" => "Dune",
    "qty" => 1,
    "price" => 14.99
];
$res = makeHttpRequest("http://localhost:8004/api/orders.php", "POST", $postOrderData);
echo "Status: " . $res['status'] . "\n";
echo "Body: " . $res['body'] . "\n\n";

$createdOrder = json_decode($res['body'], true);
$createdOrderId = $createdOrder['data']['id'] ?? 0;
$createdOrderRef = $createdOrder['data']['order_ref'] ?? '';

if ($createdOrderId > 0) {
    // Test 3: Order PUT Update Order by ID
    echo "--- Test 3: PUT Update Order (ID $createdOrderId) ---\n";
    $putOrderData = [
        "customer" => "API Test Order Customer 003 Updated",
        "email" => "apitestorder003@university.edu",
        "total" => 14.99,
        "status" => "Shipped"
    ];
    $res = makeHttpRequest("http://localhost:8004/api/orders.php?id=" . $createdOrderId, "PUT", $putOrderData);
    echo "Status: " . $res['status'] . "\n";
    echo "Body: " . $res['body'] . "\n\n";

    // Test 4: Order DELETE soft-cancel Order by Reference/ID
    echo "--- Test 4: DELETE Cancel Order (ID $createdOrderId) ---\n";
    $res = makeHttpRequest("http://localhost:8004/api/orders.php?id=" . $createdOrderId, "DELETE");
    echo "Status: " . $res['status'] . "\n";
    echo "Body: " . $res['body'] . "\n\n";
} else {
    echo "Skipping PUT/DELETE testing since order creation failed.\n";
}
