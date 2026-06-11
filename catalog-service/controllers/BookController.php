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

    /**
     * Handles retrieving a single book by ID.
     */
    public function handleGetBookById(int $id): void {
        $book = $this->model->getBookById($id);
        if ($book === null) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Book not found.'
            ], 404);
            return;
        }
        jsonResponse([
            'status' => 'success',
            'data' => $book
        ], 200);
    }

    /**
     * Handles creating a new book.
     */
    public function handleCreateBook(array $data): void {
        $title = trim($data['title'] ?? '');
        $author = trim($data['author'] ?? '');
        $isbn = trim($data['isbn'] ?? '');
        $category = trim($data['category'] ?? '');
        $price = isset($data['price']) ? (float)$data['price'] : 0.0;
        $icon = trim($data['icon'] ?? 'bi-book');
        $icon_color = trim($data['icon_color'] ?? 'text-primary');

        if (empty($title) || empty($author) || empty($isbn) || empty($category) || $price <= 0) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Title, author, isbn, category, and positive price are required.'
            ], 400);
            return;
        }

        if (!in_array($category, ['Technology', 'Fiction', 'Business', 'Science'])) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Invalid category classification.'
            ], 400);
            return;
        }

        $success = $this->model->createBook($title, $author, $isbn, $category, $price, $icon, $icon_color);
        if ($success) {
            jsonResponse([
                'status' => 'success',
                'message' => 'Book created successfully.'
            ], 201);
        } else {
            jsonResponse([
                'status' => 'error',
                'message' => 'Failed to create book (possibly duplicate ISBN or database error).'
            ], 400);
        }
    }

    /**
     * Handles updating an existing book.
     */
    public function handleUpdateBook(int $id, array $data): void {
        $title = trim($data['title'] ?? '');
        $author = trim($data['author'] ?? '');
        $isbn = trim($data['isbn'] ?? '');
        $category = trim($data['category'] ?? '');
        $price = isset($data['price']) ? (float)$data['price'] : 0.0;
        $icon = trim($data['icon'] ?? 'bi-book');
        $icon_color = trim($data['icon_color'] ?? 'text-primary');

        if (empty($title) || empty($author) || empty($isbn) || empty($category) || $price <= 0) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Title, author, isbn, category, and positive price are required.'
            ], 400);
            return;
        }

        if (!in_array($category, ['Technology', 'Fiction', 'Business', 'Science'])) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Invalid category classification.'
            ], 400);
            return;
        }

        $book = $this->model->getBookById($id);
        if ($book === null) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Book not found.'
            ], 404);
            return;
        }

        $success = $this->model->updateBook($id, $title, $author, $isbn, $category, $price, $icon, $icon_color);
        if ($success) {
            jsonResponse([
                'status' => 'success',
                'message' => 'Book updated successfully.'
            ], 200);
        } else {
            jsonResponse([
                'status' => 'error',
                'message' => 'Failed to update book.'
            ], 500);
        }
    }

    /**
     * Handles deleting a book.
     */
    public function handleDeleteBook(int $id): void {
        $book = $this->model->getBookById($id);
        if ($book === null) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Book not found.'
            ], 404);
            return;
        }

        $success = $this->model->deleteBook($id);
        if ($success) {
            jsonResponse([
                'status' => 'success',
                'message' => 'Book deleted successfully.'
            ], 200);
        } else {
            jsonResponse([
                'status' => 'error',
                'message' => 'Failed to delete book.'
            ], 500);
        }
    }
}

