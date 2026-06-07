<?php
// /frontend/login.php
$page_title = "Login - Bookshop Management System";
include_once __DIR__ . '/components/config.php';

$alert_message = '';
$alert_type = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_GET['action'] = 'login';
    
    // Capture the JSON response from user-service auth
    ob_start();
    require __DIR__ . '/../user-service/api/auth.php';
    $response = json_decode(ob_get_clean(), true);
    
    if ($response && ($response['status'] ?? '') === 'success') {
        $alert_message = $response['message'] ?? 'Login successful!';
        $alert_type = 'success';
        
        // Redirect user to home screen on successful authentication
        echo "<script>
            setTimeout(function() {
                window.location.href = 'index.php';
            }, 1500);
        </script>";
    } else {
        $alert_message = $response['message'] ?? 'Login failed.';
        $alert_type = 'danger';
    }
}

// Handle logout action
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $_GET['action'] = 'logout';
    
    // Capture response from logout handler
    ob_start();
    require __DIR__ . '/../user-service/api/auth.php';
    $response = json_decode(ob_get_clean(), true);
    
    if ($response && ($response['status'] ?? '') === 'success') {
        $alert_message = "You have been logged out successfully.";
        $alert_type = "info";
    } else {
        $alert_message = "Logout failed.";
        $alert_type = "danger";
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

  <main class="container flex-grow-1 d-flex align-items-center justify-content-center py-5">
    <div class="card premium-card w-100" style="max-width: 450px;">
      <div class="card-header card-header-gradient text-center py-4">
        <i class="bi bi-shield-lock-fill fs-1 mb-2"></i>
        <h2 class="h4 fw-bold mb-0">Sign In</h2>
        <small class="text-white-50">Access your Bookshop Account</small>
      </div>
      <div class="card-body p-4">
        
        <?php if (!empty($alert_message)): ?>
          <div class="alert alert-<?php echo $alert_type; ?> d-flex align-items-center gap-2 alert-dismissible fade show" role="alert">
            <i class="bi <?php echo ($alert_type === 'success') ? 'bi-check-circle-fill' : (($alert_type === 'danger') ? 'bi-exclamation-triangle-fill' : 'bi-info-circle-fill'); ?>"></i>
            <div><?php echo escape($alert_message); ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['user_email'])): ?>
          <div class="text-center py-4">
            <i class="bi bi-person-check-fill text-success" style="font-size: 3rem;"></i>
            <p class="mt-3 lead">Already logged in as <br><strong class="text-primary"><?php echo escape($_SESSION['user_email']); ?></strong></p>
            <div class="d-grid gap-2 mt-4">
              <a href="index.php" class="btn btn-primary rounded-pill">Proceed to Home</a>
              <a href="login.php?action=logout" class="btn btn-outline-danger rounded-pill">Sign Out / Logout</a>
            </div>
          </div>
        <?php else: ?>
          <form method="POST" action="login.php">
            <div class="mb-3">
              <label for="email" class="form-label fw-semibold">Email address</label>
              <div class="input-group">
                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" id="email" class="form-control border-start-0 ps-0" placeholder="name@university.edu" required>
              </div>
              <div class="form-text text-muted small">💡 Tip: Use email with 'admin' in it to test staff view!</div>
            </div>
            
            <div class="mb-3">
              <label for="password" class="form-label fw-semibold">Password</label>
              <div class="input-group">
                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-key"></i></span>
                <input type="password" name="password" id="password" class="form-control border-start-0 ps-0" placeholder="••••••••" required>
              </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-4">
              <div class="form-check">
                <input type="checkbox" class="form-check-input" id="remember">
                <label class="form-check-label text-secondary small" for="remember">Remember me</label>
              </div>
              <a href="#" class="text-primary text-decoration-none small hover-underline">Forgot password?</a>
            </div>

            <div class="d-grid mb-3">
              <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-semibold">
                Sign In <i class="bi bi-box-arrow-in-right ms-1"></i>
              </button>
            </div>
          </form>

          <div class="text-center mt-4">
            <p class="text-muted small mb-0">Don't have an account yet? 
              <a href="register.php" class="text-primary text-decoration-none fw-bold">Sign Up</a>
            </p>
          </div>
        <?php endif; ?>

      </div>
    </div>
  </main>

  <?php include __DIR__ . '/components/footer.php'; ?>

  <!-- Bootstrap Bundle with Popper CDN -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Shared JS logic -->
  <script src="<?php echo $base_url; ?>assets/js/shared.js"></script>
</body>
</html>
