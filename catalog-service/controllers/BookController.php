<?php
/**
 * BookController
 * Handles catalog search and filtering requests.
 */
class BookController {
    private BookModel $model;

    public function __construct(BookModel $model) {
        $this->model = $model;
    }

    /**
     * Handles book requests and prints output as JSON.
     */
    public function handleGetBooks(array $get): void {
        $category = trim($get['category'] ?? '');
        $search = trim($get['search'] ?? '');

        // 1. Process filters sequentially based on query params
        if (!empty($category) && strtolower($category) !== 'all') {
            // Retrieve catalog items filtering by category
            $books = $this->model->getBooksByCategory($category);
        } elseif (!empty($search)) {
            // Retrieve catalog items filtering by search query
            $books = $this->model->searchBooks($search);
        } else {
            // Retrieve all catalog items
            $books = $this->model->getAllBooks();
        }

        // 2. Return standard structured response
        jsonResponse([
            'status' => 'success',
            'data' => $books
        ], 200);
    }
}
