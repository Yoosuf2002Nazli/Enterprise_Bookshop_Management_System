<?php
// /frontend/books.php
$page_title = "Explore Books - Bookshop Management System";
include_once __DIR__ . '/components/config.php';

// Interactive In-Memory Book Data Store
$all_books = [
    [
        'id' => 1,
        'title' => 'Introduction to Algorithms',
        'author' => 'Thomas H. Cormen',
        'price' => 89.99,
        'category' => 'Technology',
        'stock' => 12,
        'isbn' => '978-0262033848',
        'icon' => 'bi-code-square',
        'icon_color' => 'text-primary'
    ],
    [
        'id' => 2,
        'title' => 'Clean Code',
        'author' => 'Robert C. Martin',
        'price' => 37.50,
        'category' => 'Technology',
        'stock' => 3,
        'isbn' => '978-0132350884',
        'icon' => 'bi-terminal-fill',
        'icon_color' => 'text-dark'
    ],
    [
        'id' => 3,
        'title' => 'Dune',
        'author' => 'Frank Herbert',
        'price' => 14.99,
        'category' => 'Fiction',
        'stock' => 25,
        'isbn' => '978-0441172719',
        'icon' => 'bi-compass-fill',
        'icon_color' => 'text-warning'
    ],
    [
        'id' => 4,
        'title' => 'The Lean Startup',
        'author' => 'Eric Ries',
        'price' => 24.95,
        'category' => 'Business',
        'stock' => 0,
        'isbn' => '978-0307887894',
        'icon' => 'bi-graph-up-arrow',
        'icon_color' => 'text-success'
    ],
    [
        'id' => 5,
        'title' => 'A Brief History of Time',
        'author' => 'Stephen Hawking',
        'price' => 18.99,
        'category' => 'Science',
        'stock' => 8,
        'isbn' => '978-0553380163',
        'icon' => 'bi-stars',
        'icon_color' => 'text-info'
    ],
    [
        'id' => 6,
        'title' => 'Thinking, Fast and Slow',
        'author' => 'Daniel Kahneman',
        'price' => 21.00,
        'category' => 'Science',
        'stock' => 15,
        'isbn' => '978-0374533557',
        'icon' => 'bi-brain',
        'icon_color' => 'text-danger'
    ]
];

// Read request filters
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$selected_category = isset($_GET['category']) ? trim($_GET['category']) : 'all';

// Process filtering
$filtered_books = [];
foreach ($all_books as $book) {
    // Match Category
    if ($selected_category !== 'all' && strtolower($book['category']) !== strtolower($selected_category)) {
        continue;
    }
    // Match Search Query
    if (!empty($search_query)) {
        $query = strtolower($search_query);
        $title_match = strpos(strtolower($book['title']), $query) !== false;
        $author_match = strpos(strtolower($book['author']), $query) !== false;
        $isbn_match = strpos(strtolower($book['isbn']), $query) !== false;
        if (!$title_match && !$author_match && !$isbn_match) {
            continue;
        }
    }
    $filtered_books[] = $book;
}

// Simulated action state (Add to Cart)
$cart_alert = '';
if (isset($_GET['add_cart_id'])) {
    $added_id = (int)$_GET['add_cart_id'];
    foreach ($all_books as $book) {
        if ($book['id'] === $added_id) {
            if ($book['stock'] > 0) {
                $cart_alert = "Success: <strong>" . escape($book['title']) . "</strong> has been added to your simulated cart!";
            } else {
                $cart_alert = "Error: Out of stock!";
            }
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
              <!-- Book Cover Placeholder Representation -->
              <div class="card-header bg-light border-0 py-4 d-flex justify-content-center align-items-center" style="height: 180px;">
                <div class="text-center">
                  <i class="bi <?php echo $book['icon']; ?> <?php echo $book['icon_color']; ?> display-4 mb-2"></i>
                  <div class="text-muted small fs-6">ISBN: <?php echo escape($book['isbn']); ?></div>
                </div>
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
</body>
</html>
