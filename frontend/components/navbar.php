<?php
// /frontend/components/navbar.php
if (!isset($base_url)) {
    include_once __DIR__ . '/config.php';
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-premium shadow-sm py-3 mb-4 sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-uppercase" href="<?php echo $base_url; ?>index.php">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-book-half text-warning-glow" viewBox="0 0 16 16">
        <path d="M8.5 2.687c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
      </svg>
      <span class="brand-text">Bookshop<span class="text-warning-glow fw-light">MS</span></span>
    </a>
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNavbar">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 gap-1 align-items-center">
        <li class="nav-item">
          <a class="nav-link px-3 py-2 rounded-pill" href="<?php echo $base_url; ?>index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link px-3 py-2 rounded-pill" href="<?php echo $base_url; ?>books.php">Books</a>
        </li>
        <li class="nav-item">
          <a class="nav-link px-3 py-2 rounded-pill" href="<?php echo $base_url; ?>inventory.php">Inventory</a>
        </li>
        <li class="nav-item">
          <a class="nav-link px-3 py-2 rounded-pill" href="<?php echo $base_url; ?>orders.php">Orders</a>
        </li>
        <li class="nav-item">
          <a class="nav-link px-3 py-2 rounded-pill" href="<?php echo $base_url; ?>admin.php">Admin Dashboard</a>
        </li>
        <?php if (isset($_SESSION['user_email'])): ?>
          <li class="nav-item ms-lg-2">
            <span class="nav-link text-warning-glow small">
              <?php echo escape($_SESSION['user_email']); ?>
            </span>
          </li>
          <li class="nav-item">
            <a class="btn btn-outline-light btn-sm px-3 rounded-pill" 
               href="<?php echo $base_url; ?>login.php?action=logout">Logout</a>
          </li>
        <?php else: ?>
          <li class="nav-item ms-lg-2">
            <a class="btn btn-outline-light btn-sm px-3 rounded-pill" 
               href="<?php echo $base_url; ?>login.php">Login</a>
          </li>
          <li class="nav-item">
            <a class="btn btn-warning-glow btn-sm px-3 rounded-pill text-dark fw-bold" 
               href="<?php echo $base_url; ?>register.php">Register</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
