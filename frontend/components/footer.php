<?php
// /frontend/components/footer.php
if (!isset($base_url)) {
    include_once __DIR__ . '/config.php';
}
?>
<footer class="footer mt-auto py-4 bg-dark text-white border-top border-secondary">
  <div class="container">
    <div class="row align-items-center justify-content-between g-3">
      <div class="col-md-6 text-center text-md-start">
        <span class="text-secondary">&copy; <?php echo date("Y"); ?> </span>
        <strong class="text-light">Bookshop Management System</strong>
        <span class="text-secondary"> | Academic Enterprise Project</span>
      </div>
      <div class="col-md-6 text-center text-md-end">
        <ul class="list-inline mb-0">
          <li class="list-inline-item"><a href="<?php echo $base_url; ?>index.php" class="text-secondary text-decoration-none hover-light">Home</a></li>
          <li class="list-inline-item text-muted">&bull;</li>
          <li class="list-inline-item"><a href="<?php echo $base_url; ?>books.php" class="text-secondary text-decoration-none hover-light">Books</a></li>
          <li class="list-inline-item text-muted">&bull;</li>
          <li class="list-inline-item"><a href="<?php echo $base_url; ?>inventory.php" class="text-secondary text-decoration-none hover-light">Inventory</a></li>
          <li class="list-inline-item text-muted">&bull;</li>
          <li class="list-inline-item"><a href="<?php echo $base_url; ?>orders.php" class="text-secondary text-decoration-none hover-light">Orders</a></li>
        </ul>
      </div>
    </div>
  </div>
</footer>
