<?php
// /frontend/admin.php
$page_title = "Admin Dashboard - Bookshop Management System";
include_once __DIR__ . '/components/config.php';

// Access Guard: Ensure user is logged in as admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// 1. Fetch live inventory from inventory-service
ob_start();
$old_get = $_GET;
$_GET = [];
require __DIR__ . '/../inventory-service/api/inventory.php';
$inv_response = json_decode(ob_get_clean(), true);
$inventory = $inv_response['data'] ?? [];
$_GET = $old_get; // restore

// Populate session inventory to support downstream HTML rendering loops
$_SESSION['inventory_db'] = $inventory;

// 2. Fetch live orders from order-service
ob_start();
$old_get = $_GET;
$_GET = [];
require __DIR__ . '/../order-service/api/orders.php';
$orders_response = json_decode(ob_get_clean(), true);
$orders = $orders_response['data'] ?? [];
$_GET = $old_get; // restore

// 3. Calculate Real-time Dashboard Metrics from live data
$catalog_size = count($inventory);

$low_stock_count = 0;
foreach ($inventory as $item) {
    if ($item['stock'] <= $item['threshold']) {
        $low_stock_count++;
    }
}

$pending_orders_count = 0;
$total_revenue = 0.0;
foreach ($orders as $order) {
    if ($order['status'] === 'Pending') {
        $pending_orders_count++;
    }
    if ($order['status'] !== 'Cancelled') {
        $total_revenue += (float)$order['total'];
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

  <main class="container flex-grow-1 mb-5">
    
    <!-- Dashboard Header -->
    <div class="row align-items-center mb-4 g-3">
      <div class="col-md-8 text-center text-md-start">
        <h1 class="h2 fw-bold mb-1">Administrative Dashboard</h1>
        <p class="text-secondary mb-0">Aggregate metrics across service silos, oversee orders, and review low stock alerts.</p>
      </div>
      <div class="col-md-4 text-center text-md-end">
        <div class="text-muted small">Logged in as: <strong class="text-primary"><?php echo escape($_SESSION['user_email'] ?? 'System Administrator'); ?></strong></div>
      </div>
    </div>

    <!-- Quick Metrics Cards -->
    <div class="row g-3 mb-5">
      <!-- Metric 1: Total Revenue -->
      <div class="col-6 col-lg-3">
        <div class="card premium-card border-0 shadow-sm h-100 text-center py-3">
          <div class="card-body">
            <div class="p-2 bg-success bg-opacity-10 text-success rounded-circle d-inline-flex mb-2">
              <i class="bi bi-cash-stack fs-4 px-1"></i>
            </div>
            <span class="text-secondary small fw-bold d-block text-uppercase">Total Sales Revenue</span>
            <strong class="h3 fw-bold text-dark font-monospace d-block mt-1">$<?php echo number_format($total_revenue, 2); ?></strong>
            <small class="text-success small fw-semibold"><i class="bi bi-graph-up me-1"></i>Active Orders</small>
          </div>
        </div>
      </div>

      <!-- Metric 2: Catalog Size -->
      <div class="col-6 col-lg-3">
        <div class="card premium-card border-0 shadow-sm h-100 text-center py-3">
          <div class="card-body">
            <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex mb-2">
              <i class="bi bi-journals fs-4 px-1"></i>
            </div>
            <span class="text-secondary small fw-bold d-block text-uppercase">Catalog Listings</span>
            <strong class="h3 fw-bold text-dark font-monospace d-block mt-1"><?php echo $catalog_size; ?></strong>
            <small class="text-primary small fw-semibold"><i class="bi bi-tags me-1"></i>Published entries</small>
          </div>
        </div>
      </div>

      <!-- Metric 3: Pending Checkouts -->
      <div class="col-6 col-lg-3">
        <div class="card premium-card border-0 shadow-sm h-100 text-center py-3">
          <div class="card-body">
            <div class="p-2 bg-warning bg-opacity-10 text-warning rounded-circle d-inline-flex mb-2">
              <i class="bi bi-hourglass-split fs-4 px-1"></i>
            </div>
            <span class="text-secondary small fw-bold d-block text-uppercase">Pending Checkouts</span>
            <strong class="h3 fw-bold text-dark font-monospace d-block mt-1"><?php echo $pending_orders_count; ?></strong>
            <small class="text-warning small fw-semibold"><i class="bi bi-exclamation-circle me-1"></i>Requires actions</small>
          </div>
        </div>
      </div>

      <!-- Metric 4: Low Stock Alerts -->
      <div class="col-6 col-lg-3">
        <div class="card premium-card border-0 shadow-sm h-100 text-center py-3 <?php echo ($low_stock_count > 0) ? 'border-warning-glow low-stock-pulse' : ''; ?>">
          <div class="card-body">
            <div class="p-2 bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex mb-2">
              <i class="bi bi-exclamation-triangle fs-4 px-1"></i>
            </div>
            <span class="text-secondary small fw-bold d-block text-uppercase">Low Stock Alerts</span>
            <strong class="h3 fw-bold text-danger font-monospace d-block mt-1"><?php echo $low_stock_count; ?></strong>
            <small class="text-danger small fw-semibold"><i class="bi bi-arrow-down-circle me-1"></i>Needs restock</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Navigation Panels & Logs -->
    <div class="row g-4 mb-4">
      <!-- Quick Portal Navigation -->
      <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100">
          <div class="card-body">
            <h3 class="h5 fw-bold mb-3 border-bottom pb-2 text-dark"><i class="bi bi-sliders me-1"></i> Quick Portals</h3>
            <div class="d-grid gap-2">
              <a href="books.php" class="btn btn-outline-primary d-flex align-items-center justify-content-between text-start rounded-pill px-3 py-2 fw-semibold">
                <span><i class="bi bi-book-half me-2"></i> Browse Catalog</span>
                <i class="bi bi-arrow-right"></i>
              </a>
              <a href="inventory.php" class="btn btn-outline-success d-flex align-items-center justify-content-between text-start rounded-pill px-3 py-2 fw-semibold">
                <span><i class="bi bi-boxes me-2"></i> Manage Stock</span>
                <i class="bi bi-arrow-right"></i>
              </a>
              <a href="orders.php" class="btn btn-outline-info d-flex align-items-center justify-content-between text-start rounded-pill px-3 py-2 fw-semibold">
                <span><i class="bi bi-receipt me-2"></i> Track Orders</span>
                <i class="bi bi-arrow-right"></i>
              </a>
              <a href="login.php?action=logout" class="btn btn-outline-danger d-flex align-items-center justify-content-between text-start rounded-pill px-3 py-2 fw-semibold mt-4">
                <span><i class="bi bi-box-arrow-left me-2"></i> Staff Sign Out</span>
                <i class="bi bi-power"></i>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Critical Low-Stock Activity Alert Grid -->
      <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
          <div class="card-header bg-light border-0 py-3 px-4">
            <div class="row align-items-center">
              <div class="col-8">
                <h3 class="h5 fw-bold text-dark mb-0"><i class="bi bi-exclamation-octagon text-danger me-1"></i> Critical Stock Logs</h3>
              </div>
              <div class="col-4 text-end">
                <a href="inventory.php?filter=low" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-semibold">Replenish All</a>
              </div>
            </div>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr class="small text-muted">
                    <th scope="col" class="ps-4">Book Title</th>
                    <th scope="col" class="text-center">Current Qty</th>
                    <th scope="col" class="text-center">Status</th>
                    <th scope="col" class="text-end pe-4">Quick Replenish</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $low_items_rendered = 0;
                  foreach ($_SESSION['inventory_db'] as $item):
                    if ($item['stock'] <= $item['threshold']):
                      $low_items_rendered++;
                  ?>
                    <tr>
                      <td class="ps-4">
                        <div class="fw-bold text-dark small"><?php echo escape($item['title']); ?></div>
                        <small class="text-secondary small font-monospace">ISBN: <?php echo escape($item['isbn']); ?></small>
                      </td>
                      <td class="text-center font-monospace fw-bold text-danger"><?php echo $item['stock']; ?></td>
                      <td class="text-center">
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle badge-pill-status">Low Stock</span>
                      </td>
                      <td class="text-end pe-4">
                        <a href="inventory.php?action=restock&id=<?php echo $item['id']; ?>&qty=20" class="btn btn-outline-success btn-xs px-2 py-1 rounded-pill small fw-semibold">
                          +20 Bulk
                        </a>
                      </td>
                    </tr>
                  <?php 
                    endif;
                  endforeach; 
                  
                  if ($low_items_rendered === 0):
                  ?>
                    <tr>
                      <td colspan="4" class="text-center py-5 text-success fw-semibold">
                        <i class="bi bi-shield-check fs-2 d-block mb-2"></i>
                        Excellent! All stock levels are safely above thresholds.
                      </td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
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
