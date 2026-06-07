<?php
// /frontend/inventory.php
$page_title = "Inventory Levels - Bookshop Management System";
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

$alert_message = '';
$alert_type = 'success';

// Handle stock level modification (restocking)
if (isset($_GET['action']) && $_GET['action'] === 'restock') {
    $item_id = (int)($_GET['id'] ?? 0);
    $qty = (int)($_GET['qty'] ?? 10);
    
    // Call inventory-service to restock item
    $old_get = $_GET;
    $_GET = [
        'action' => 'restock',
        'id' => $item_id,
        'qty' => $qty
    ];
    
    ob_start();
    require __DIR__ . '/../inventory-service/api/inventory.php';
    $restock_res = json_decode(ob_get_clean(), true);
    $_GET = $old_get; // restore GET
    
    if ($restock_res && ($restock_res['status'] ?? '') === 'success') {
        $alert_message = "Stock replenished! Added <strong>+{$qty}</strong> copies for item ID #{$item_id}.";
        $alert_type = "success";
    } else {
        $alert_message = $restock_res['message'] ?? "Failed to replenish stock.";
        $alert_type = "danger";
    }
}

// Read stock filters
$filter_low_stock = isset($_GET['filter']) && $_GET['filter'] === 'low';
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Load inventory data from API
$old_get = $_GET;
$_GET = [];
if ($filter_low_stock) {
    $_GET['filter'] = 'low';
}

ob_start();
require __DIR__ . '/../inventory-service/api/inventory.php';
$inv_response = json_decode(ob_get_clean(), true);
$inventory_data = $inv_response['data'] ?? [];
$_GET = $old_get; // restore GET

// Filter by search query if set
$filtered_inventory = [];
foreach ($inventory_data as $item) {
    if (!empty($search_query)) {
        $q = strtolower($search_query);
        if (strpos(strtolower($item['title']), $q) === false && strpos(strtolower($item['isbn']), $q) === false) {
            continue;
        }
    }
    $filtered_inventory[] = $item;
}
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
    
    <!-- Top Header Summary -->
    <div class="row align-items-center mb-4 g-3">
      <div class="col-md-6">
        <h1 class="h2 fw-bold mb-1">Inventory Management</h1>
        <p class="text-secondary mb-0">Track real-time catalog stock levels, thresholds, and replenishment pipelines.</p>
      </div>
      <div class="col-md-6 text-md-end">
        <div class="btn-group rounded-pill overflow-hidden border">
          <a href="inventory.php" class="btn <?php echo !$filter_low_stock ? 'btn-primary' : 'btn-light'; ?> btn-sm px-3 fw-semibold">
            All Stock
          </a>
          <a href="inventory.php?filter=low" class="btn <?php echo $filter_low_stock ? 'btn-danger' : 'btn-light'; ?> btn-sm px-3 fw-semibold">
            <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>Low Stock Only
          </a>
        </div>
      </div>
    </div>

    <!-- Feedback messages -->
    <?php if (!empty($alert_message)): ?>
      <div class="alert alert-<?php echo $alert_type; ?> d-flex align-items-center gap-2 alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-patch-check-fill fs-5"></i>
        <div><?php echo $alert_message; ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <!-- Search Toolbar -->
    <div class="card border-0 shadow-sm p-3 mb-4 rounded-3 bg-white">
      <form method="GET" action="inventory.php" class="row g-3">
        <?php if ($filter_low_stock): ?>
          <input type="hidden" name="filter" value="low">
        <?php endif; ?>
        <div class="col-md-8">
          <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Search by Book Title or ISBN..." value="<?php echo escape($search_query); ?>">
            <button class="btn btn-primary px-4 fw-semibold" type="submit">Search</button>
          </div>
        </div>
        <div class="col-md-4 text-md-end d-flex align-items-center justify-content-md-end justify-content-start gap-2">
          <small class="text-secondary">Low Stock Indicator Threshold:</small>
          <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill font-monospace"><= Threshold Qty</span>
        </div>
      </form>
    </div>

    <!-- Inventory Data Table -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
      <div class="table-responsive">
        <table class="data-table">
          <thead class="table-light">
            <tr>
              <th scope="col" class="ps-4">Book Title</th>
              <th scope="col">ISBN</th>
              <th scope="col">Category</th>
              <th scope="col" class="text-center">Min. Threshold</th>
              <th scope="col" class="text-center">Current Stock</th>
              <th scope="col" class="text-center">Status</th>
              <th scope="col" class="text-end pe-4">Stock Replenishment Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($filtered_inventory)): ?>
              <tr>
                <td colspan="7" class="text-center py-5 text-secondary">
                  <i class="bi bi-inboxes display-4 text-muted d-block mb-3"></i>
                  No inventory records matched your filters.
                </td>
              </tr>
            <?php else: 
              foreach ($filtered_inventory as $item):
                $is_low = $item['stock'] <= $item['threshold'];
                $is_out = $item['stock'] === 0;
                $row_class = $is_low ? ($is_out ? 'table-danger-subtle' : 'table-warning-subtle') : '';
                $pulse_class = $is_low ? 'low-stock-pulse' : '';
            ?>
              <tr class="<?php echo $row_class; ?>">
                <td class="ps-4">
                  <div class="fw-bold text-dark"><?php echo escape($item['title']); ?></div>
                </td>
                <td class="font-monospace text-secondary small"><?php echo escape($item['isbn']); ?></td>
                <td>
                  <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-2 py-1 small"><?php echo escape($item['category']); ?></span>
                </td>
                <td class="text-center font-monospace text-secondary fw-semibold"><?php echo $item['threshold']; ?></td>
                <td class="text-center">
                  <div class="d-flex align-items-center justify-content-center gap-2">
                    <?php
                      $max_display = $item['threshold'] * 2;
                      $fill_pct = $max_display > 0
                        ? min(100, round(($item['stock'] / $max_display) * 100))
                        : 100;
                      $bar_class = $is_out ? 'empty' : ($is_low ? 'low' : '');
                    ?>
                    <div class="stock-bar-wrap">
                      <div class="stock-bar-fill <?php echo $bar_class; ?>"
                           style="width:<?php echo $fill_pct; ?>%"></div>
                    </div>
                    <span class="font-monospace fw-bold
                      <?php echo $is_out
                        ? 'text-danger'
                        : ($is_low ? 'text-warning' : 'text-dark'); ?>">
                      <?php echo $item['stock']; ?>
                    </span>
                  </div>
                </td>
                <td class="text-center">
                  <?php if ($is_out): ?>
                    <span class="badge bg-danger text-white badge-pill-status">
                      <i class="bi bi-x-circle-fill me-1"></i>Empty
                    </span>
                  <?php elseif ($is_low): ?>
                    <span class="badge bg-warning text-dark badge-pill-status">
                      <i class="bi bi-exclamation-triangle-fill me-1"></i>Low Stock
                    </span>
                  <?php else: ?>
                    <span class="badge bg-success text-white badge-pill-status">
                      <i class="bi bi-check-circle-fill me-1"></i>Normal
                    </span>
                  <?php endif; ?>
                </td>
                <td class="text-end pe-4">
                  <!-- Simulated Quick Restocks -->
                  <div class="d-inline-flex gap-1">
                    <a href="inventory.php?<?php echo $filter_low_stock ? 'filter=low&' : ''; ?>search=<?php echo urlencode($search_query); ?>&action=restock&id=<?php echo $item['id']; ?>&qty=5" 
                       class="btn btn-outline-success btn-sm fw-semibold rounded-pill px-3" title="Replenish 5 Units">
                      +5 Restock
                    </a>
                    <a href="inventory.php?<?php echo $filter_low_stock ? 'filter=low&' : ''; ?>search=<?php echo urlencode($search_query); ?>&action=restock&id=<?php echo $item['id']; ?>&qty=20" 
                       class="btn btn-success btn-sm text-white fw-semibold rounded-pill px-3" title="Replenish 20 Units">
                      +20 Bulk
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; 
            endif; ?>
          </tbody>
        </table>
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
