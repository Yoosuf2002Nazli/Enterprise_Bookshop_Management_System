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

    /**
     * Create a new book.
     */
    public function createBook(
        string $title,
        string $author,
        string $isbn,
        string $category,
        float $price,
        ?string $icon,
        ?string $icon_color
    ): bool {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO books (title, author, isbn, category, price, icon, icon_color) 
                VALUES (:title, :author, :isbn, :category, :price, :icon, :icon_color)
            ");
            return $stmt->execute([
                ':title' => $title,
                ':author' => $author,
                ':isbn' => $isbn,
                ':category' => $category,
                ':price' => $price,
                ':icon' => $icon ?: 'bi-book',
                ':icon_color' => $icon_color ?: 'text-primary'
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Update an existing book's details.
     */
    public function updateBook(
        int $id,
        string $title,
        string $author,
        string $isbn,
        string $category,
        float $price,
        ?string $icon,
        ?string $icon_color
    ): bool {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE books 
                SET title = :title, author = :author, isbn = :isbn, 
                    category = :category, price = :price, 
                    icon = :icon, icon_color = :icon_color 
                WHERE id = :id
            ");
            return $stmt->execute([
                ':title' => $title,
                ':author' => $author,
                ':isbn' => $isbn,
                ':category' => $category,
                ':price' => $price,
                ':icon' => $icon ?: 'bi-book',
                ':icon_color' => $icon_color ?: 'text-primary',
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Delete a book by ID.
     */
    public function deleteBook(int $id): bool {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM books WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}

