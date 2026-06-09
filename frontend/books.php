<?php
// /frontend/books.php
$page_title = "Explore Books - Bookshop Management System";
include_once __DIR__ . '/components/config.php';

function getBookCoverClass(string $category): string {
  return 'cover-' . strtolower($category);
}

// 1. Read request filters
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$selected_category = isset($_GET['category']) ? trim($_GET['category']) : 'all';

$cart_alert = '';

// 2. Handle Add to Cart action (Order Placement)
if (isset($_GET['add_cart_id'])) {
    $added_id = (int)$_GET['add_cart_id'];
    
    // Check if the user is logged in
    if (!isset($_SESSION['user_email'])) {
        $cart_alert = "Error: <strong>Please log in</strong> to purchase books.";
    } else {
        // We need the book details (title, price) to place the order
        // First retrieve all books from catalog to find the book info
        ob_start();
        $old_get = $_GET;
        $_GET = []; // clear to get all books
        require __DIR__ . '/../catalog-service/api/books.php';
        $cat_response = json_decode(ob_get_clean(), true);
        $books_data = $cat_response['data'] ?? [];
        $_GET = $old_get; // restore

        $target_book = null;
        foreach ($books_data as $b) {
            if ((int)$b['id'] === $added_id) {
                $target_book = $b;
                break;
            }
        }

        if ($target_book === null) {
            $cart_alert = "Error: Book not found in the catalog.";
        } else {
            // Place the order via order-service POST action=create
            $old_post = $_POST;
            $old_get = $_GET;
            
            $_POST = [
                'customer' => $_SESSION['user_fullname'] ?? $_SESSION['user_email'],
                'email' => $_SESSION['user_email'],
                'book_id' => $target_book['id'],
                'book_title' => $target_book['title'],
                'qty' => 1,
                'price' => $target_book['price']
            ];
            $_GET = ['action' => 'create'];
            
            ob_start();
            require __DIR__ . '/../order-service/api/orders.php';
            $order_res = json_decode(ob_get_clean(), true);
            
            $_POST = $old_post; // restore $_POST
            $_GET = $old_get;   // restore $_GET
            
            if ($order_res && ($order_res['status'] ?? '') === 'success') {
                $order_ref = $order_res['order_ref'] ?? '';
                $cart_alert = "Success: Order <strong>" . escape($order_ref) . "</strong> has been created! <strong>" . escape($target_book['title']) . "</strong> has been purchased.";
                
                // Call notification-service POST action=log to log order placement
                $old_post_notif = $_POST;
                $old_get_notif = $_GET;
                $_POST = [
                    'type' => 'order_placed',
                    'message' => 'Order placed by ' . $_SESSION['user_email'],
                    'reference_id' => $order_ref
                ];
                $_GET = ['action' => 'log'];
                
                ob_start();
                require __DIR__ . '/../notification-service/api/notify.php';
                ob_get_clean(); // discard notification response
                
                $_POST = $old_post_notif;
                $_GET = $old_get_notif;
            } else {
                $cart_alert = "Error: " . ($order_res['message'] ?? 'Failed to place order.');
            }
        }
    }
}

// 3. Load active stock levels from inventory-service
ob_start();
$old_get = $_GET;
$_GET = []; // clear to load all inventory levels
require __DIR__ . '/../inventory-service/api/inventory.php';
$inv_response = json_decode(ob_get_clean(), true);
$inventory_data = $inv_response['data'] ?? [];
$_GET = $old_get; // restore

$stock_map = [];
foreach ($inventory_data as $inv) {
    $stock_map[$inv['isbn']] = (int)$inv['stock'];
}

// 4. Retrieve filtered/searched books from catalog-service
ob_start();
// catalog-service/api/books.php resolves $_GET['category'] and $_GET['search']
require __DIR__ . '/../catalog-service/api/books.php';
$cat_response = json_decode(ob_get_clean(), true);
$books_data = $cat_response['data'] ?? [];

// 5. Merge stock levels into book catalog list
$filtered_books = [];
foreach ($books_data as $book) {
    $isbn = $book['isbn'];
    $book['stock'] = $stock_map[$isbn] ?? 0;
    $filtered_books[] = $book;
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
    
    <!-- Header Summary -->
    <div class="row align-items-center mb-4 g-3">
      <div class="col-md-6">
        <h1 class="h2 fw-bold mb-1">Book Catalog</h1>
        <p class="text-secondary mb-0">Browse academic, programming, and literary catalog additions.</p>
      </div>
      <div class="col-md-6 text-md-end">
        <span class="badge bg-primary px-3 py-2 rounded-pill"><?php echo count($filtered_books); ?> Items Found</span>
      </div>
    </div>

    <!-- Alert triggers -->
    <?php if (!empty($cart_alert)): ?>
      <div class="alert alert-success d-flex align-items-center gap-2 alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-cart-check-fill fs-5"></i>
        <div><?php echo $cart_alert; ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <!-- Filter Toolbar -->
    <div class="card border-0 shadow-sm p-3 mb-4 rounded-3 bg-white">
      <form method="GET" action="books.php" class="row g-3">
        <!-- Category Pill Selectors -->
        
        <div class="col-12 col-lg-8 d-flex flex-wrap gap-2 align-items-center">
          <span class="text-secondary small fw-bold text-uppercase me-2">Categories:</span>
          <?php 
          $categories = ['all' => 'All Books', 'technology' => 'Technology', 'fiction' => 'Fiction', 'business' => 'Business', 'science' => 'Science'];
          foreach ($categories as $val => $label):
            $btn_class = ($selected_category === $val) ? 'btn-primary' : 'btn-outline-secondary';
          ?>
            <a href="books.php?category=<?php echo $val; ?>&search=<?php echo urlencode($search_query); ?>" 
               class="btn <?php echo $btn_class; ?> btn-sm px-3 rounded-pill fw-semibold">
              <?php echo $label; ?>
            </a>
          <?php endforeach; ?>
        </div>

        <!-- Search Bar input -->
        <div class="col-12 col-lg-4">
          <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search title, author, or ISBN..." value="<?php echo escape($search_query); ?>">
            <button class="btn btn-primary px-3" type="submit">
              <i class="bi bi-search"></i>
            </button>
            <?php if (!empty($search_query) || $selected_category !== 'all'): ?>
              <a href="books.php" class="btn btn-outline-danger" title="Clear Filters">
                <i class="bi bi-x-circle"></i>
              </a>
            <?php endif; ?>
          </div>
        </div>
      </form>
    </div>

    <!-- Books Card Grid -->
    <?php if (empty($filtered_books)): ?>
      <div class="text-center py-5 rounded-4 shadow-sm bg-white mt-4 border border-dashed">
        <i class="bi bi-journal-x text-muted" style="font-size: 4rem;"></i>
        <h3 class="h5 fw-bold mt-3 text-secondary">No Books Found</h3>
        <p class="text-muted">No catalog listings match your search criteria. Try modifying filters or search query.</p>
        <a href="books.php" class="btn btn-primary rounded-pill px-4">Reset Filters</a>
      </div>
    <?php else: ?>
      <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($filtered_books as $book): ?>
          <div class="col">
            <div class="card h-100 premium-card">
              <div class="book-card-cover
                <?php echo getBookCoverClass($book['category']); ?>">
                <i class="bi <?php echo $book['icon']; ?> book-icon"></i>
                <span class="book-isbn">
                  <?php echo escape($book['isbn']); ?>
                </span>
              </div>
              
              <!-- Card details -->
              <div class="card-body d-flex flex-column p-4">
                <div class="mb-2">
                  <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-2 py-1 small"><?php echo escape($book['category']); ?></span>
                </div>
                <h3 class="card-title h5 fw-bold text-dark mb-1 lh-sm"><?php echo escape($book['title']); ?></h3>
                <p class="text-muted small mb-3">By <?php echo escape($book['author']); ?></p>
                
                <div class="d-flex align-items-center justify-content-between mt-auto pt-3 border-top">
                  <div>
                    <span class="text-muted small">Price</span>
                    <div class="h4 fw-bold text-primary mb-0">$<?php echo number_format($book['price'], 2); ?></div>
                  </div>
                  
                  <div class="text-end">
                    <?php if ($book['stock'] > 0): ?>
                      <span class="badge bg-success-subtle text-success border-success-subtle badge-pill-status mb-2">
                        <i class="bi bi-check-circle me-1"></i>In Stock (<?php echo $book['stock']; ?>)
                      </span>
                    <?php else: ?>
                      <span class="badge bg-danger-subtle text-danger border-danger-subtle badge-pill-status mb-2">
                        <i class="bi bi-x-circle me-1"></i>Out of Stock
                      </span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
              
              <!-- Card actions -->
              <div class="card-footer bg-white border-0 p-4 pt-0">
                <div class="d-grid">
                  <?php if ($book['stock'] > 0): ?>
                    <a href="books.php?category=<?php echo urlencode($selected_category); ?>&search=<?php echo urlencode($search_query); ?>&add_cart_id=<?php echo $book['id']; ?>" class="btn btn-primary rounded-pill fw-semibold shadow-sm">
                      <i class="bi bi-cart-plus me-1"></i>Add to Cart
                    </a>
                  <?php else: ?>
                    <button class="btn btn-secondary rounded-pill fw-semibold" disabled>
                      <i class="bi bi-envelope me-1"></i>Notify Restock
                    </button>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

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
