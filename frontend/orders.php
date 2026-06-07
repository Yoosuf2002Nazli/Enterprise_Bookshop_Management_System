<?php
// /frontend/orders.php
$page_title = "Customer Orders - Bookshop Management System";
include_once __DIR__ . '/components/config.php';

// Access Guard: Ensure user is logged in as admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$alert_message = '';
$alert_type = 'success';

// Handle order status update
if (isset($_GET['action']) && isset($_GET['id'])) {
    $order_id = $_GET['id'];
    $new_status = $_GET['action'];
    
    $valid_statuses = ['Pending', 'Shipped', 'Delivered', 'Cancelled'];
    if (in_array($new_status, $valid_statuses)) {
        // Call order-service to update status
        $old_get = $_GET;
        $_GET = [
            'action' => 'update_status',
            'id' => $order_id,
            'status' => $new_status
        ];
        
        ob_start();
        require __DIR__ . '/../order-service/api/orders.php';
        $update_res = json_decode(ob_get_clean(), true);
        $_GET = $old_get; // restore GET
        
        if ($update_res && ($update_res['status'] ?? '') === 'success') {
            $alert_message = "Order <strong>{$order_id}</strong> has been successfully marked as <strong>{$new_status}</strong>.";
            $alert_type = ($new_status === 'Cancelled') ? 'danger' : 'success';
            
            // Call notification-service to log the status change
            $old_post_notif = $_POST;
            $old_get_notif = $_GET;
            $_POST = [
                'type' => 'status_update',
                'message' => "Order {$order_id} updated to {$new_status}",
                'reference_id' => $order_id
            ];
            $_GET = ['action' => 'log'];
            
            ob_start();
            require __DIR__ . '/../notification-service/api/notify.php';
            ob_get_clean(); // discard response
            
            $_POST = $old_post_notif;
            $_GET = $old_get_notif;
        } else {
            $alert_message = $update_res['message'] ?? "Failed to update order status.";
            $alert_type = "danger";
        }
    }
}

// Load orders from API
$old_get = $_GET;
$_GET = [];
ob_start();
require __DIR__ . '/../order-service/api/orders.php';
$orders_response = json_decode(ob_get_clean(), true);
$orders_data = $orders_response['data'] ?? [];
$_GET = $old_get; // restore GET

// Map order data structure to match frontend expectations (id = order_ref, date = created_at formatted)
$orders = [];
foreach ($orders_data as $order) {
    $order['id'] = $order['order_ref'];
    $order['date'] = date('Y-m-d h:i A', strtotime($order['created_at']));
    $orders[] = $order;
}

// Find selected order details if requested
$selected_order = null;
if (isset($_GET['view_id'])) {
    $view_id = $_GET['view_id'];
    foreach ($orders as $order) {
        if ($order['id'] === $view_id) {
            $selected_order = $order;
            break;
        }
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
    
    <!-- Top Header Summary -->
    <div class="row align-items-center mb-4 g-3">
      <div class="col-md-8">
        <h1 class="h2 fw-bold mb-1">Customer Order Log</h1>
        <p class="text-secondary mb-0">Review pending checkouts, processing operations, and shipped book dispatches.</p>
      </div>
      <div class="col-md-4 text-md-end">
        <span class="badge bg-secondary-subtle text-secondary-emphasis px-3 py-2 rounded-pill fw-semibold">
          Total Orders Logged: <?php echo count($_SESSION['orders_db']); ?>
        </span>
      </div>
    </div>

    <!-- Alert notification -->
    <?php if (!empty($alert_message)): ?>
      <div class="alert alert-<?php echo $alert_type; ?> d-flex align-items-center gap-2 alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-info-circle-fill fs-5"></i>
        <div><?php echo $alert_message; ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <div class="row g-4">
      <!-- Main Table Column -->
      <div class="<?php echo ($selected_order) ? 'col-lg-7' : 'col-12'; ?> transition-all">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
          <div class="table-responsive">
            <table class="data-table">
              <thead class="table-light">
                <tr>
                  <th scope="col" class="ps-4">Order ID</th>
                  <th scope="col">Customer</th>
                  <th scope="col">Date</th>
                  <th scope="col" class="text-center">Total Amount</th>
                  <th scope="col" class="text-center">Status</th>
                  <th scope="col" class="text-end pe-4">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($_SESSION['orders_db'] as $order): 
                  // Configure status badge colors
                  $status = $order['status'];
                  $badge_color = 'bg-secondary';
                  if ($status === 'Pending') $badge_color = 'bg-warning text-dark';
                  elseif ($status === 'Shipped') $badge_color = 'bg-primary';
                  elseif ($status === 'Delivered') $badge_color = 'bg-success';
                  elseif ($status === 'Cancelled') $badge_color = 'bg-danger';
                ?>
                  <tr class="<?php echo ($selected_order && $selected_order['id'] === $order['id']) ? 'table-active fw-bold' : ''; ?>">
                    <td class="ps-4">
                      <a href="orders.php?view_id=<?php echo $order['id']; ?>" class="text-primary font-monospace text-decoration-none hover-underline small fw-bold">
                        <?php echo escape($order['id']); ?>
                      </a>
                    </td>
                    <td>
                      <div class="fw-semibold text-dark"><?php echo escape($order['customer']); ?></div>
                      <small class="text-muted small"><?php echo escape($order['email']); ?></small>
                    </td>
                    <td class="text-secondary small"><?php echo $order['date']; ?></td>
                    <td class="text-center font-monospace text-dark fw-semibold">$<?php echo number_format($order['total'], 2); ?></td>
                    <td class="text-center">
                      <?php
                        $dot_color = match($status) {
                          'Pending'   => '#f59e0b',
                          'Shipped'   => '#3b82f6',
                          'Delivered' => '#10b981',
                          'Cancelled' => '#ef4444',
                          default     => '#94a3b8'
                        };
                      ?>
                      <div class="d-flex align-items-center justify-content-center gap-2">
                        <span class="status-dot"
                          style="background-color:<?php echo $dot_color; ?>"></span>
                        <span class="badge <?php echo $badge_color; ?> badge-pill-status">
                          <?php echo $status; ?>
                        </span>
                      </div>
                    </td>
                    <td class="text-end pe-4">
                      <!-- Dropdown Action list -->
                      <div class="btn-group">
                        <a href="orders.php?view_id=<?php echo $order['id']; ?>" class="btn btn-light btn-sm rounded-start-pill border border-end-0 px-3 fw-semibold">
                          <i class="bi bi-eye-fill"></i> View
                        </a>
                        <button type="button" class="btn btn-light btn-sm dropdown-toggle dropdown-toggle-split rounded-end-pill border" data-bs-toggle="dropdown" aria-expanded="false">
                          <span class="visually-hidden">Toggle Status</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                          <li><h6 class="dropdown-header">Modify Status Pipeline</h6></li>
                          <li><a class="dropdown-item d-flex align-items-center gap-2" href="orders.php?view_id=<?php echo $order['id']; ?>&id=<?php echo $order['id']; ?>&action=Pending"><i class="bi bi-hourglass text-warning"></i> Pending</a></li>
                          <li><a class="dropdown-item d-flex align-items-center gap-2" href="orders.php?view_id=<?php echo $order['id']; ?>&id=<?php echo $order['id']; ?>&action=Shipped"><i class="bi bi-truck text-primary"></i> Shipped</a></li>
                          <li><a class="dropdown-item d-flex align-items-center gap-2" href="orders.php?view_id=<?php echo $order['id']; ?>&id=<?php echo $order['id']; ?>&action=Delivered"><i class="bi bi-check-circle text-success"></i> Delivered</a></li>
                          <li><hr class="dropdown-divider"></li>
                          <li><a class="dropdown-item d-flex align-items-center gap-2 text-danger" href="orders.php?view_id=<?php echo $order['id']; ?>&id=<?php echo $order['id']; ?>&action=Cancelled"><i class="bi bi-x-circle text-danger"></i> Cancel Order</a></li>
                        </ul>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Detail Card View Column (Shown dynamically if view_id selected) -->
      <?php if ($selected_order): ?>
        <div class="col-lg-5">
          <div class="card premium-card border-0 shadow-sm">
            <div class="card-header card-header-gradient d-flex align-items-center justify-content-between py-3">
              <div class="d-flex align-items-center gap-2">
                <i class="bi bi-receipt fs-4"></i>
                <h2 class="h5 fw-bold mb-0">Invoice Overview</h2>
              </div>
              <a href="orders.php" class="btn-close btn-close-white" aria-label="Close"></a >
            </div>
            <div class="card-body p-4">
              <!-- Invoice Meta Details -->
              <div class="row g-3 mb-4">
                <div class="col-6">
                  <small class="text-secondary text-uppercase fw-semibold d-block">Order Reference</small>
                  <strong class="font-monospace text-primary"><?php echo escape($selected_order['id']); ?></strong>
                </div>
                <div class="col-6 text-end">
                  <small class="text-secondary text-uppercase fw-semibold d-block">Status</small>
                  <?php 
                    $curr_status = $selected_order['status'];
                    $b_color = 'bg-secondary';
                    if ($curr_status === 'Pending') $b_color = 'bg-warning text-dark';
                    elseif ($curr_status === 'Shipped') $b_color = 'bg-primary';
                    elseif ($curr_status === 'Delivered') $b_color = 'bg-success';
                    elseif ($curr_status === 'Cancelled') $b_color = 'bg-danger';
                  ?>
                  <span class="badge <?php echo $b_color; ?> badge-pill-status mt-1"><?php echo $curr_status; ?></span>
                </div>
                <div class="col-12 border-top pt-3">
                  <small class="text-secondary text-uppercase fw-semibold d-block">Customer Details</small>
                  <strong class="text-dark d-block"><?php echo escape($selected_order['customer']); ?></strong>
                  <span class="text-muted small"><?php echo escape($selected_order['email']); ?></span>
                </div>
                <div class="col-12">
                  <small class="text-secondary text-uppercase fw-semibold d-block">Timestamp</small>
                  <span class="text-dark small"><?php echo $selected_order['date']; ?></span>
                </div>
              </div>

              <!-- Item Log -->
              <h3 class="h6 fw-bold text-uppercase text-secondary border-bottom pb-2 mb-3">Purchased Book Items</h3>
              <div class="table-responsive">
                <table class="table table-sm table-borderless align-middle mb-4">
                  <thead>
                    <tr class="text-muted small">
                      <th scope="col">Title</th>
                      <th scope="col" class="text-center">Qty</th>
                      <th scope="col" class="text-end">Price</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($selected_order['items'] as $item): ?>
                      <tr>
                        <td class="text-dark fw-semibold small"><?php echo escape($item['title']); ?></td>
                        <td class="text-center font-monospace text-secondary"><?php echo $item['qty']; ?></td>
                        <td class="text-end font-monospace text-secondary">$<?php echo number_format($item['price'] * $item['qty'], 2); ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                  <tfoot>
                    <tr class="border-top">
                      <td colspan="2" class="fw-bold text-dark pt-3">Grand Total:</td>
                      <td class="text-end font-monospace fw-bold text-primary fs-5 pt-3">$<?php echo number_format($selected_order['total'], 2); ?></td>
                    </tr>
                  </tfoot>
                </table>
              </div>

              <!-- Invoice Actions -->
              <div class="d-grid gap-2">
                <button class="btn btn-outline-primary rounded-pill fw-semibold" onclick="window.print()">
                  <i class="bi bi-printer me-1"></i> Print Invoice Record
                </button>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>
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
