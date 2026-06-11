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
            $columnsStmt = $this->pdo->query("DESCRIBE books");
            $columns = $columnsStmt->fetchAll(PDO::FETCH_COLUMN);

            $fields = ['title', 'author', 'isbn', 'category', 'price'];
            $placeholders = [':title', ':author', ':isbn', ':category', ':price'];
            $params = [
                ':title' => $title,
                ':author' => $author,
                ':isbn' => $isbn,
                ':category' => $category,
                ':price' => $price
            ];

            if (in_array('icon', $columns)) {
                $fields[] = 'icon';
                $placeholders[] = ':icon';
                $params[':icon'] = $icon ?: 'bi-book';
            }

            if (in_array('icon_color', $columns)) {
                $fields[] = 'icon_color';
                $placeholders[] = ':icon_color';
                $params[':icon_color'] = $icon_color ?: 'text-primary';
            }

            $sql = sprintf(
                "INSERT INTO books (%s) VALUES (%s)",
                implode(', ', $fields),
                implode(', ', $placeholders)
            );

            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
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
            // 1. Inspect table columns to handle different schemas
            $columnsStmt = $this->pdo->query("DESCRIBE books");
            $columns = $columnsStmt->fetchAll(PDO::FETCH_COLUMN);
            
            // 2. Build SQL query dynamically
            $fields = [
                'title = :title' => $title,
                'author = :author' => $author,
                'isbn = :isbn' => $isbn,
                'category = :category' => $category,
                'price = :price' => $price
            ];
            
            $params = [
                ':title' => $title,
                ':author' => $author,
                ':isbn' => $isbn,
                ':category' => $category,
                ':price' => $price,
                ':id' => $id
            ];
            
            if (in_array('icon', $columns)) {
                $fields['icon = :icon'] = $icon ?: 'bi-book';
                $params[':icon'] = $icon ?: 'bi-book';
            }
            
            if (in_array('icon_color', $columns)) {
                $fields['icon_color = :icon_color'] = $icon_color ?: 'text-primary';
                $params[':icon_color'] = $icon_color ?: 'text-primary';
            }
            
            $sql = "UPDATE books SET " . implode(', ', array_keys($fields)) . " WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
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

