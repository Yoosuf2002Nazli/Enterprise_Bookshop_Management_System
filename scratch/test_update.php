<?php
// scratch/test_update.php

require_once __DIR__ . '/../shared/database/connection_template.php';
require_once __DIR__ . '/../catalog-service/models/BookModel.php';

$dbConfig = require __DIR__ . '/../catalog-service/config/config.php';

try {
    $pdo = createPdoConnection($dbConfig);
    // Enable error exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $bookModel = new BookModel($pdo);
    
    // Attempt the update directly and display errors if they occur
    $id = 4;
    $title = "API Test Book Updated";
    $author = "Test Author Updated";
    $isbn = "978-0000000002";
    $category = "Technology";
    $price = 49.99;
    $icon = "bi-book";
    $icon_color = "text-primary";
    
    // We will bypass the model's catch block by running the query directly
    $stmt = $pdo->prepare("
        UPDATE books 
        SET title = :title, author = :author, isbn = :isbn, 
            category = :category, price = :price, 
            icon = :icon, icon_color = :icon_color 
        WHERE id = :id
    ");
    $success = $stmt->execute([
        ':title' => $title,
        ':author' => $author,
        ':isbn' => $isbn,
        ':category' => $category,
        ':price' => $price,
        ':icon' => $icon,
        ':icon_color' => $icon_color,
        ':id' => $id
    ]);
    
    echo "Update executed: " . ($success ? "Success" : "Failed") . "\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
