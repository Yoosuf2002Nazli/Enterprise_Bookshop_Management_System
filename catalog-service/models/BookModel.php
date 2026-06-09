<?php
/**
 * BookModel
 * Data access layer for catalog_db.books table.
 */
class BookModel {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Retrieve all books from the catalog.
     */
    public function getAllBooks(): array {
        try {
            // Select all rows ordered by their creation time
            $stmt = $this->pdo->query("SELECT * FROM books ORDER BY id ASC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Retrieve books matching a specific category.
     */
    public function getBooksByCategory(string $category): array {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM books WHERE category = :category ORDER BY id ASC");
            $stmt->execute([':category' => $category]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Search books by title, author, or ISBN.
     */
    public function searchBooks(string $query): array {
        try {
            // Bind search query using wildcard match pattern
            $wildcardQuery = "%" . $query . "%";
            $stmt = $this->pdo->prepare("
                SELECT * FROM books 
                WHERE title LIKE :query 
                   OR author LIKE :query 
                   OR isbn LIKE :query 
                ORDER BY id ASC
            ");
            $stmt->execute([':query' => $wildcardQuery]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Retrieve details of a single book by ID.
     */
    public function getBookById(int $id): ?array {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM books WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $book = $stmt->fetch();
            return $book ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }
}
