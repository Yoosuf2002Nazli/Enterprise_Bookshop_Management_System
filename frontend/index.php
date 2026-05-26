<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookshop Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="container py-5">
        <h1 class="mb-3">Bookshop Management System</h1>
        <p class="lead">Semester project starter using beginner-friendly microservices-style modules.</p>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h2 class="h5">Customer Portal</h2>
                        <p class="mb-3">Browse and purchase books.</p>
                        <a class="btn btn-primary" href="customer/index.php">Open Customer View</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h2 class="h5">Admin Portal</h2>
                        <p class="mb-3">Manage catalog, inventory, and orders.</p>
                        <a class="btn btn-outline-primary" href="admin/index.php">Open Admin View</a>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="h5">Service Endpoints (Starter)</h2>
        <ul>
            <li><code>../user-service/api/index.php</code></li>
            <li><code>../catalog-service/api/index.php</code></li>
            <li><code>../inventory-service/api/index.php</code></li>
            <li><code>../order-service/api/index.php</code></li>
            <li><code>../notification-service/api/index.php</code></li>
        </ul>
    </div>

    <script src="assets/js/app.js"></script>
</body>
</html>
