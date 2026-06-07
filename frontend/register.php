<?php
// /frontend/register.php
$page_title = "Register - Bookshop Management System";
include_once __DIR__ . '/components/config.php';

$alert_message = '';
$alert_type = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Set route action for user-service registration
    $_GET['action'] = 'register';
    
    // Capture the JSON response from user-service
    ob_start();
    require __DIR__ . '/../user-service/api/auth.php';
    $response = json_decode(ob_get_clean(), true);
    
    if ($response && ($response['status'] ?? '') === 'success') {
        $alert_message = $response['message'] ?? 'Registration successful!';
        $alert_type = 'success';
        
        // Redirect user to login portal on successful registration
        echo "<script>
            setTimeout(function() {
                window.location.href = 'login.php';
            }, 1500);
        </script>";
    } else {
        $alert_message = $response['message'] ?? 'Registration failed.';
        $alert_type = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo escape($page_title); ?></title>
  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons CDN -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- Shared Stylesheet -->
  <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/shared.css">
</head>
<body class="d-flex flex-column min-vh-100">
  <?php include __DIR__ . '/components/navbar.php'; ?>

  <main class="flex-grow-1">
    <div class="split-auth-layout">

      <div class="split-auth-brand">
        <div class="text-center">
          <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64"
            fill="currentColor"
            class="bi bi-person-plus-fill mb-4 text-warning-glow"
            viewBox="0 0 16 16">
            <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
            <path fill-rule="evenodd" d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5z"/>
          </svg>
          <h2 class="fw-bold fs-1 mb-2">Join BookshopMS</h2>
          <p class="text-white-50 fs-5 mb-4">
            Create your account to access the bookstore platform.
          </p>
          <div class="d-flex flex-column gap-3 text-start mt-3">
            <div class="d-flex align-items-center gap-3">
              <i class="bi bi-check-circle-fill text-warning"></i>
              <span>Browse and search the full catalog</span>
            </div>
            <div class="d-flex align-items-center gap-3">
              <i class="bi bi-check-circle-fill text-warning"></i>
              <span>Place and track your orders</span>
            </div>
            <div class="d-flex align-items-center gap-3">
              <i class="bi bi-check-circle-fill text-warning"></i>
              <span>Get notified on restocks</span>
            </div>
          </div>
        </div>
      </div>

      <div class="split-auth-form">
        <div class="w-100" style="max-width:420px;">
          <div class="mb-4">
            <h2 class="h4 fw-bold mb-1">Create Account</h2>
            <p class="text-muted small">Join the Bookshop Management System</p>
          </div>

          <?php if (!empty($alert_message)): ?>
            <div class="alert alert-<?php echo $alert_type; ?> d-flex align-items-center gap-2 alert-dismissible fade show" role="alert">
              <i class="bi <?php echo ($alert_type === 'success') ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'; ?>"></i>
              <div><?php echo escape($alert_message); ?></div>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>

          <form method="POST" action="register.php">
            <div class="mb-3">
              <label for="fullname" class="form-label fw-semibold">Full Name</label>
              <div class="input-group">
                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-person"></i></span>
                <input type="text" name="fullname" id="fullname" class="form-control border-start-0 ps-0" placeholder="John Doe" required>
              </div>
            </div>

            <div class="mb-3">
              <label for="email" class="form-label fw-semibold">Email address</label>
              <div class="input-group">
                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" id="email" class="form-control border-start-0 ps-0" placeholder="johndoe@university.edu" required>
              </div>
            </div>

            <div class="mb-3">
              <label for="role" class="form-label fw-semibold">Register As (Account Type)</label>
              <div class="input-group">
                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-people"></i></span>
                <select name="role" id="role" class="form-select border-start-0 ps-0">
                  <option value="customer">Customer (Student / Reader)</option>
                  <option value="staff">Staff (Bookstore Manager / Administrator)</option>
                </select>
              </div>
              <div class="form-text text-muted small">💡 Tip: Registering as Staff configures admin flags.</div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="password" class="form-label fw-semibold">Password</label>
                <div class="input-group">
                  <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-key"></i></span>
                  <input type="password" name="password" id="password" class="form-control border-start-0 ps-0" placeholder="••••••••" required>
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="confirm_password" class="form-label fw-semibold">Confirm Password</label>
                <div class="input-group">
                  <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-key"></i></span>
                  <input type="password" name="confirm_password" id="confirm_password" class="form-control border-start-0 ps-0" placeholder="••••••••" required>
                </div>
              </div>
            </div>

            <div class="form-check mb-4">
              <input type="checkbox" class="form-check-input" id="terms" required>
              <label class="form-check-label text-secondary small" for="terms">I agree to the Academic Code of Conduct and terms.</label>
            </div>

            <div class="d-grid mb-3">
              <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-semibold">
                Sign Up <i class="bi bi-check-lg ms-1"></i>
              </button>
            </div>
          </form>

          <div class="text-center mt-4">
            <p class="text-muted small mb-0">Already have an account? 
              <a href="login.php" class="text-primary text-decoration-none fw-bold">Sign In</a>
            </p>
          </div>

        </div>
      </div>

    </div>
  </main>

  <?php include __DIR__ . '/components/footer.php'; ?>

  <!-- Bootstrap Bundle with Popper CDN -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Shared JS logic -->
  <script src="<?php echo $base_url; ?>assets/js/shared.js"></script>
  <!-- UI Enhancement logic -->
  <script src="<?php echo $base_url; ?>assets/js/ui.js"></script>
</body>
</html>
