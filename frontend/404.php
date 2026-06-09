<?php
// /frontend/404.php
$page_title = "404 - Page Not Found - Bookshop Management System";
include_once __DIR__ . '/components/config.php';
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
    <div class="card premium-card border-0 shadow-lg text-center p-5 w-100" style="max-width: 600px; background: #ffffff;">
      <div class="card-body">
        <!-- Error Code & Icon -->
        <div class="mb-4 d-inline-flex p-4 bg-danger bg-opacity-10 text-danger rounded-circle animate-bounce">
          <i class="bi bi-journal-x display-1"></i>
        </div>
        <h1 class="display-3 fw-extrabold text-dark tracking-tight mb-2 font-monospace">404</h1>
        <h2 class="h4 fw-bold text-secondary mb-3">Lost in the Shelves?</h2>
        
        <!-- Helpful text -->
        <p class="text-muted mb-5">
          The page you are looking for does not exist, has been archived, or was moved. Let's get you back to the catalog or your main dashboard.
        </p>

        <!-- Navigation Portals -->
        <div class="row g-3">
          <div class="col-sm-6">
            <a href="<?php echo $base_url; ?>index.php" class="btn btn-outline-primary rounded-pill w-100 fw-bold py-2">
              <i class="bi bi-house-door-fill me-2"></i>Go to Home
            </a>
          </div>
          <div class="col-sm-6">
            <a href="<?php echo $base_url; ?>books.php" class="btn btn-primary btn-warning-glow rounded-pill w-100 fw-bold py-2 text-dark">
              <i class="bi bi-book-half me-2"></i>Explore Books
            </a>
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
</body>
</html>
