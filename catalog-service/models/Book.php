<?php
// catalog-service/models/Book.php
/**
 * Book Model - Data Access Layer for Catalog Service
 * Handles all book-related database operations
 */

class Book {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get all books with optional filtering
     * @param array $filters ['category_id', 'is_active', 'search']
     * @return array List of books
     */
    public function getAll($filters = []) {
        $query = "SELECT b.*, c.name as category_name FROM books b 
                  LEFT JOIN categories c ON b.category_id = c.id 
                  WHERE 1=1";
        $params = [];

        if (isset($filters['category_id']) && !empty($filters['category_id'])) {
            $query .= " AND b.category_id = ?";
            $params[] = $filters['category_id'];
        }

        if (!isset($filters['include_inactive']) || !$filters['include_inactive']) {
            $query .= " AND b.is_active = 1";
        }

        if (isset($filters['search']) && !empty($filters['search'])) {
            $searchTerm = '%' . $filters['search'] . '%';
            $query .= " AND (b.title LIKE ? OR b.author LIKE ? OR b.isbn LIKE ?)";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $query .= " ORDER BY b.title ASC";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Get single book by ID
     * @param int $id Book ID
     * @return array|null Book record
     */
    public function getById($id) {
        $stmt = $this->pdo->prepare(
            "SELECT b.*, c.name as category_name FROM books b 
             LEFT JOIN categories c ON b.category_id = c.id 
             WHERE b.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Get book by ISBN
     * @param string $isbn ISBN code
     * @return array|null Book record
     */
    public function getByIsbn($isbn) {
        $stmt = $this->pdo->prepare(
            "SELECT b.*, c.name as category_name FROM books b 
             LEFT JOIN categories c ON b.category_id = c.id 
             WHERE b.isbn = ?"
        );
        $stmt->execute([$isbn]);
        return $stmt->fetch();
    }

    /**
     * Create new book
     * @param array $data Book data ['title', 'author', 'isbn', 'price', 'category_id', ...]
     * @return int|false Book ID on success, false on failure
     */
    public function create($data) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO books (isbn, title, author, description, price, category_id, publisher, publication_year, pages, language, is_active)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $result = $stmt->execute([
            $data['isbn'] ?? null,
            $data['title'] ?? null,
            $data['author'] ?? null,
            $data['description'] ?? null,
            $data['price'] ?? null,
            $data['category_id'] ?? null,
            $data['publisher'] ?? null,
            $data['publication_year'] ?? null,
            $data['pages'] ?? null,
            $data['language'] ?? 'English',
            $data['is_active'] ?? true
        ]);

        return $result ? $this->pdo->lastInsertId() : false;
    }

    /**
     * Update book record
     * @param int $id Book ID
     * @param array $data Updated book data
     * @return bool Success status
     */
    public function update($id, $data) {
        $updateFields = [];
        $params = [];

        $allowedFields = ['title', 'author', 'description', 'price', 'category_id', 'publisher', 'publication_year', 'pages', 'language', 'is_active'];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateFields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }

        if (empty($updateFields)) {
            return false;
        }

        $updateFields[] = "updated_at = CURRENT_TIMESTAMP";
        $params[] = $id;

        $query = "UPDATE books SET " . implode(', ', $updateFields) . " WHERE id = ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute($params);
    }

    /**
     * Delete book (soft delete by marking inactive)
     * @param int $id Book ID
     * @return bool Success status
     */
    public function delete($id) {
        $stmt = $this->pdo->prepare("UPDATE books SET is_active = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Get books by category
     * @param int $categoryId Category ID
     * @return array List of books in category
     */
    public function getByCategory($categoryId) {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM books WHERE category_id = ? AND is_active = 1 ORDER BY title ASC"
        );
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll();
    }

    /**
     * Search books by multiple criteria
     * @param string $query Search query
     * @return array Search results
     */
    public function search($query) {
        return $this->getAll(['search' => $query]);
    }

    /**
     * Count total active books
     * @return int Total count
     */
    public function countActive() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM books WHERE is_active = 1");
        return (int)$stmt->fetchColumn();
    }

    /**
     * Get books with pagination
     * @param int $page Page number (1-indexed)
     * @param int $perPage Items per page
     * @return array ['data' => books, 'total' => total_count, 'page' => current_page, 'pages' => total_pages]
     */
    public function getPaginated($page = 1, $perPage = 10) {
        $total = $this->countActive();
        $pages = (int)ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;

        $stmt = $this->pdo->prepare(
            "SELECT b.*, c.name as category_name FROM books b 
             LEFT JOIN categories c ON b.category_id = c.id 
             WHERE b.is_active = 1 
             ORDER BY b.title ASC 
             LIMIT ? OFFSET ?"
        );
        $stmt->execute([$perPage, $offset]);

        return [
            'data' => $stmt->fetchAll(),
            'total' => $total,
            'page' => $page,
            'pages' => $pages,
            'per_page' => $perPage
        ];
    }
}