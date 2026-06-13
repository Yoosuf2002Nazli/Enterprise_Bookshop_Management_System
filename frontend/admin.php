<?php
// /frontend/admin.php
$page_title = "Admin Dashboard - Bookshop Management System";
include_once __DIR__ . '/components/config.php';

// Access Guard: Ensure user is logged in as admin or staff, or email has 'admin'
$is_admin = false;
if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
    if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'staff')) {
        $is_admin = true;
    }
    if (isset($_SESSION['user_email']) && strpos($_SESSION['user_email'], 'admin') !== false) {
        $is_admin = true;
    }
}

if (!$is_admin) {
    header('Location: login.php');
    exit;
}

// 1. Fetch live inventory from inventory-service
$inv_response = makeServiceRequest(INVENTORY_SERVICE_URL, 'GET');
$inventory = $inv_response['data'] ?? [];

// Populate session inventory to support downstream HTML rendering loops
$_SESSION['inventory_db'] = $inventory;

// 2. Fetch live orders from order-service
$orders_response = makeServiceRequest(ORDER_SERVICE_URL, 'GET');
$orders = $orders_response['data'] ?? [];

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

$shipped_count = 0;
$delivered_count = 0;
$cancelled_count = 0;
foreach ($orders as $order) {
    if ($order['status'] === 'Shipped')   $shipped_count++;
    if ($order['status'] === 'Delivered') $delivered_count++;
    if ($order['status'] === 'Cancelled') $cancelled_count++;
}
$normal_stock_count = $catalog_size - $low_stock_count;
header('Content-Type: text/html; charset=utf-8');
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
    <div class="row g-3 mb-4">
      <div class="col-6 col-lg-3">
        <div class="stat-card accent-green">
          <p class="stat-label">Total Sales Revenue</p>
          <div class="stat-value">
            $<?php echo number_format($total_revenue, 2); ?>
          </div>
          <div class="stat-trend text-success">
            <i class="bi bi-graph-up me-1"></i>Active Orders
          </div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card accent-blue">
          <p class="stat-label">Catalog Listings</p>
          <div class="stat-value"><?php echo $catalog_size; ?></div>
          <div class="stat-trend text-primary">
            <i class="bi bi-tags me-1"></i>Published entries
          </div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card accent-yellow">
          <p class="stat-label">Pending Checkouts</p>
          <div class="stat-value">
            <?php echo $pending_orders_count; ?>
          </div>
          <div class="stat-trend text-warning">
            <i class="bi bi-exclamation-circle me-1"></i>Requires action
          </div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card accent-red
          <?php echo ($low_stock_count > 0) ? 'low-stock-pulse' : ''; ?>">
          <p class="stat-label">Low Stock Alerts</p>
          <div class="stat-value text-danger">
            <?php echo $low_stock_count; ?>
          </div>
          <div class="stat-trend text-danger">
            <i class="bi bi-arrow-down-circle me-1"></i>Needs restock
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
      <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-3 p-4">
          <h3 class="h6 fw-bold text-uppercase text-muted mb-3">
            Order Status Distribution
          </h3>
          <div style="position:relative; height:220px;">
            <canvas id="ordersBarChart"></canvas>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 p-4">
          <h3 class="h6 fw-bold text-uppercase text-muted mb-3">
            Stock Health
          </h3>
          <canvas id="stockDoughnutChart"></canvas>
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
              <table class="data-table">
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
  <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- Charts Initialization -->
  <script>
  // Order Status Bar Chart
  new Chart(document.getElementById('ordersBarChart'), {
    type: 'bar',
    data: {
      labels: ['Pending','Shipped','Delivered','Cancelled'],
      datasets: [{
        data: [
          <?php echo $pending_orders_count; ?>,
          <?php echo $shipped_count; ?>,
          <?php echo $delivered_count; ?>,
          <?php echo $cancelled_count; ?>
        ],
        backgroundColor: ['#f59e0b','#3b82f6','#10b981','#ef4444'],
        borderRadius: 6,
        borderSkipped: false
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { stepSize: 1 },
          grid: { color: '#f1f5f9' }
        },
        x: { grid: { display: false } }
      }
    }
  });

  // Stock Health Doughnut Chart
  new Chart(document.getElementById('stockDoughnutChart'), {
    type: 'doughnut',
    data: {
      labels: ['Normal Stock','Low Stock'],
      datasets: [{
        data: [
          <?php echo $normal_stock_count; ?>,
          <?php echo $low_stock_count; ?>
        ],
        backgroundColor: ['#10b981','#ef4444'],
        borderWidth: 0
      }]
    },
    options: {
      cutout: '70%',
      plugins: {
        legend: {
          position: 'bottom',
          labels: { font: { size: 11 }, padding: 16 }
        }
      }
    }
  });
  </script>
  <!-- Shared JS logic -->
  <script src="<?php echo $base_url; ?>assets/js/shared.js"></script>
  <!-- UI Enhancement logic -->
  <script src="<?php echo $base_url; ?>assets/js/ui.js"></script>
</body>
</html>
