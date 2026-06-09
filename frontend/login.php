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

  <main class="flex-grow-1">
    <div class="split-auth-layout">

      <div class="split-auth-brand">
        <div class="text-center">
          <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64"
            fill="currentColor"
            class="bi bi-book-half mb-4 text-warning-glow"
            viewBox="0 0 16 16">
            <path d="M8.5 2.687c.654-.689 1.782-.886 3.112-.752
              1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692
              -3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8
              1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672
              -3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455
              c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087
              3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019
              3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0
              16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952
              -3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
          </svg>
          <h2 class="fw-bold fs-1 mb-2">BookshopMS</h2>
          <p class="text-white-50 fs-5 mb-4">
            Enterprise bookstore management<br>
            for academic institutions.
          </p>
          <div class="d-flex flex-column gap-3 text-start mt-3">
            <div class="d-flex align-items-center gap-3">
              <i class="bi bi-check-circle-fill text-warning"></i>
              <span>Manage catalog and inventory</span>
            </div>
            <div class="d-flex align-items-center gap-3">
              <i class="bi bi-check-circle-fill text-warning"></i>
              <span>Track customer orders</span>
            </div>
            <div class="d-flex align-items-center gap-3">
              <i class="bi bi-check-circle-fill text-warning"></i>
              <span>Real-time stock alerts</span>
            </div>
          </div>
        </div>
      </div>

      <div class="split-auth-form">
        <div class="w-100" style="max-width:420px;">
          <div class="mb-4">
            <h2 class="h4 fw-bold mb-1">Sign In</h2>
            <p class="text-muted small">Access your Bookshop Account</p>
          </div>

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
