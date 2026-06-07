<?php
/**
 * AuthController
 * Handles register, login, logout request routing.
 */
class AuthController {
    private UserModel $model;

    public function __construct(UserModel $model) {
        $this->model = $model;
    }

    /**
     * Handles account registration.
     */
    public function handleRegister(array $post): void {
        $fullname = trim($post['fullname'] ?? '');
        $email = trim($post['email'] ?? '');
        $password = $post['password'] ?? '';
        $confirmPassword = $post['confirm_password'] ?? '';
        $role = trim($post['role'] ?? 'customer');

        // 1. Validate all required parameters are present
        if (empty($fullname) || empty($email) || empty($password) || empty($confirmPassword)) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Fullname, email, password, and confirm password are required.'
            ], 400);
            return;
        }

        // 2. Validate role is acceptable
        if (!in_array($role, ['customer', 'staff'])) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Invalid role classification.'
            ], 400);
            return;
        }

        // 3. Validate password mismatch
        if ($password !== $confirmPassword) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Passwords do not match.'
            ], 400);
            return;
        }

        // 4. Validate password length minimum
        if (strlen($password) < 6) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Password must be at least 6 characters long.'
            ], 400);
            return;
        }

        // 5. Check duplicate email check
        $existingUser = $this->model->findByEmail($email);
        if ($existingUser !== null) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Email address is already registered.'
            ], 409);
            return;
        }

        // 6. Generate hash and attempt database creation
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $success = $this->model->createUser($fullname, $email, $passwordHash, $role);

        if ($success) {
            jsonResponse([
                'status' => 'success',
                'message' => 'User registered successfully.'
            ], 201);
        } else {
            jsonResponse([
                'status' => 'error',
                'message' => 'Failed to create user account due to system error.'
            ], 500);
        }
    }

    /**
     * Handles authentication login.
     */
    public function handleLogin(array $post): void {
        $email = trim($post['email'] ?? '');
        $password = $post['password'] ?? '';

        // 1. Validate parameters are present
        if (empty($email) || empty($password)) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Email and password are required.'
            ], 400);
            return;
        }

        // 2. Lookup user by email
        $user = $this->model->findByEmail($email);
        if ($user === null) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Invalid email or password.'
            ], 401);
            return;
        }

        // 3. Verify password hash
        if (!password_verify($password, $user['password_hash'])) {
            jsonResponse([
                'status' => 'error',
                'message' => 'Invalid email or password.'
            ], 401);
            return;
        }

        // 4. Populate Session variables safely
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_fullname'] = $user['fullname'];
        $_SESSION['is_logged_in'] = true;

        // 5. Send successful response
        jsonResponse([
            'status' => 'success',
            'message' => 'Login successful.',
            'email' => $user['email'],
            'role' => $user['role']
        ], 200);
    }

    /**
     * Handles logging out and clearing sessions.
     */
    public function handleLogout(): void {
        // Destroy active session and delete globals
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();

        jsonResponse([
            'status' => 'success',
            'message' => 'Logout successful.'
        ], 200);
    }
}
