<?php
// catalog-service/models/Category.php
/**
 * Category Model - Data Access Layer for Categories
 * Manages book categories and classification
 */

class Category {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get all categories
     * @return array List of all categories
     */
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM categories ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    /**
     * Get category by ID
     * @param int $id Category ID
     * @return array|null Category record
     */
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Get category by slug
     * @param string $slug URL-friendly category name
     * @return array|null Category record
     */
    public function getBySlug($slug) {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }

    /**
     * Create new category
     * @param array $data Category data ['name', 'description', 'slug']
     * @return int|false Category ID on success
     */
    public function create($data) {
        $slug = $data['slug'] ?? $this->generateSlug($data['name'] ?? '');
        
        $stmt = $this->pdo->prepare(
            "INSERT INTO categories (name, description, slug) VALUES (?, ?, ?)"
        );

        $result = $stmt->execute([
            $data['name'] ?? null,
            $data['description'] ?? null,
            $slug
        ]);

        return $result ? $this->pdo->lastInsertId() : false;
    }

    /**
     * Update category
     * @param int $id Category ID
     * @param array $data Updated data
     * @return bool Success status
     */
    public function update($id, $data) {
        $fields = [];
        $params = [];

        if (isset($data['name'])) {
            $fields[] = "name = ?";
            $params[] = $data['name'];
        }

        if (isset($data['description'])) {
            $fields[] = "description = ?";
            $params[] = $data['description'];
        }

        if (isset($data['slug'])) {
            $fields[] = "slug = ?";
            $params[] = $data['slug'];
        }

        if (empty($fields)) return false;

        $fields[] = "updated_at = CURRENT_TIMESTAMP";
        $params[] = $id;

        $stmt = $this->pdo->prepare("UPDATE categories SET " . implode(', ', $fields) . " WHERE id = ?");
        return $stmt->execute($params);
    }

    /**
     * Delete category
     * @param int $id Category ID
     * @return bool Success status
     */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Get category with book count
     * @param int $id Category ID
     * @return array|null Category with book count
     */
    public function getWithBookCount($id) {
        $stmt = $this->pdo->prepare(
            "SELECT c.*, COUNT(b.id) as book_count FROM categories c 
             LEFT JOIN books b ON c.id = b.category_id AND b.is_active = 1
             WHERE c.id = ? GROUP BY c.id"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Get all categories with book counts
     * @return array Categories with counts
     */
    public function getAllWithCounts() {
        $stmt = $this->pdo->query(
            "SELECT c.*, COUNT(b.id) as book_count FROM categories c 
             LEFT JOIN books b ON c.id = b.category_id AND b.is_active = 1
             GROUP BY c.id ORDER BY c.name ASC"
        );
        return $stmt->fetchAll();
    }

    /**
     * Generate URL-friendly slug from name
     * @param string $name Category name
     * @return string Slug
     */
    private function generateSlug($name) {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        return trim($slug, '-');
    }
}