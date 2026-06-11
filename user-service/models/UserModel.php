<?php
/**
 * UserModel
 * Data access layer for user_db.users table.
 */
class UserModel {
    private PDO $pdo;

    /**
     * Constructor accepts a PDO connection instance.
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Find user by email.
     * Returns the user row array on success, or null if not found.
     */
    public function findByEmail(string $email): ?array {
        try {
            // Prepare selection query to fetch user record by email
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();
            return $user ?: null;
        } catch (PDOException $e) {
            // Log database exceptions in production environments
            return null;
        }
    }

    /**
     * Insert new user.
     * Returns true on success, false on duplicate email or database exception.
     */
    public function createUser(
        string $fullname,
        string $email,
        string $passwordHash,
        string $role
    ): bool {
        try {
            // Check first if user with same email exists
            if ($this->findByEmail($email) !== null) {
                return false;
            }

            // Prepare statement to insert user details
            $stmt = $this->pdo->prepare("
                INSERT INTO users (fullname, email, password_hash, role) 
                VALUES (:fullname, :email, :password_hash, :role)
            ");
            
            return $stmt->execute([
                ':fullname' => $fullname,
                ':email' => $email,
                ':password_hash' => $passwordHash,
                ':role' => $role
            ]);
        } catch (PDOException $e) {
            // Catch database errors such as constraint violations
            return false;
        }
    }

    /**
     * Retrieve all users.
     */
    public function getAllUsers(): array {
        try {
            $stmt = $this->pdo->query("SELECT id, fullname, email, role, created_at FROM users ORDER BY id ASC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Retrieve a single user by ID.
     */
    public function getUserById(int $id): ?array {
        try {
            $stmt = $this->pdo->prepare("SELECT id, fullname, email, role, created_at FROM users WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $user = $stmt->fetch();
            return $user ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Update user details.
     */
    public function updateUser(int $id, string $fullname, string $email, string $role): bool {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET fullname = :fullname, email = :email, role = :role 
                WHERE id = :id
            ");
            return $stmt->execute([
                ':fullname' => $fullname,
                ':email' => $email,
                ':role' => $role,
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Delete user by ID.
     */
    public function deleteUser(int $id): bool {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}

