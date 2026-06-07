<?php
// /frontend/index.php
$page_title = "Home - Bookshop Management System";
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

  <main class="container flex-grow-1 mb-5">
    <!-- Hero Banner Section -->
    <div class="p-5 mb-5 hero-card rounded-4 border-0 shadow-lg text-white" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
      <div class="row align-items-center g-4">
        <div class="col-lg-8 py-3">
          <span class="badge bg-warning text-dark fw-bold mb-3 px-3 py-2 text-uppercase">Academic Project Sandbox</span>
          <h1 class="display-3 fw-bold mb-3 tracking-tight">Enterprise Bookshop Management System</h1>
          <p class="lead text-light-50 fs-4 mb-4">
            An advanced, beginner-friendly PHP microservices platform. Structure clean catalogs, administer inventory stock, and track client orders inside an elegant dashboard.
          </p>
          <div class="d-flex flex-wrap gap-3">
            <a href="books.php" class="btn btn-warning btn-lg px-4 py-3 fw-bold shadow">
              <i class="bi bi-book-half me-2"></i>Explore Catalog
            </a>
            <a href="admin.php" class="btn btn-outline-light btn-lg px-4 py-3 fw-semibold">
              <i class="bi bi-speedometer2 me-2"></i>Admin Panel
            </a>
          </div>
        </div>
        <div class="col-lg-4 text-center d-none d-lg-block">
          <i class="bi bi-book-half" style="font-size: 10rem; opacity: 0.15;"></i>
        </div>
      </div>
    </div>

    <!-- Core Features Grid -->
    <h2 class="h4 fw-bold text-uppercase text-secondary tracking-wider mb-4 text-center text-md-start">Explore Microservice Portals</h2>
    <div class="row g-4 mb-5">
      <!-- Portal 1: Customer View -->
      <div class="col-md-6">
        <div class="card h-100 premium-card p-2">
          <div class="card-body d-flex flex-column">
            <div class="d-flex align-items-center mb-3">
              <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-3 me-3">
                <i class="bi bi-shop fs-3"></i>
              </div>
              <div>
                <h3 class="h5 fw-bold mb-0">Customer Portal</h3>
                <small class="text-muted">Interactive Bookstore Frontend</small>
              </div>
            </div>
            <p class="card-text text-secondary mb-4 flex-grow-1">
              Browse published catalog entries, search by categories or authors, view detailed pricing, and simulate order placement in a responsive user layout.
            </p>
            <a href="books.php" class="btn btn-outline-primary fw-semibold mt-auto align-self-start px-4 py-2 rounded-pill">
              Browse Bookstore <i class="bi bi-arrow-right ms-1"></i>
            </a>
          </div>
        </div>
      </div>

      <!-- Portal 2: Admin Dashboard -->
      <div class="col-md-6">
        <div class="card h-100 premium-card p-2">
          <div class="card-body d-flex flex-column">
            <div class="d-flex align-items-center mb-3">
              <div class="p-3 bg-success bg-opacity-10 text-success rounded-3 me-3">
                <i class="bi bi-shield-check fs-3"></i>
              </div>
              <div>
                <h3 class="h5 fw-bold mb-0">Staff Dashboard</h3>
                <small class="text-muted">Catalog & Stock Controls</small>
              </div>
            </div>
            <p class="card-text text-secondary mb-4 flex-grow-1">
              Check inventory stock thresholds, oversee order processing pipelines, edit book catalog entries, and examine overall store performance metrics in real-time.
            </p>
            <a href="admin.php" class="btn btn-outline-success fw-semibold mt-auto align-self-start px-4 py-2 rounded-pill">
              Open Dashboard <i class="bi bi-arrow-right ms-1"></i>
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
