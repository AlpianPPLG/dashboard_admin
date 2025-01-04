<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Search and filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Base query
$query = "SELECT orders.*, users.username FROM orders JOIN users ON orders.user_id = users.id";
$countQuery = "SELECT COUNT(*) FROM orders JOIN users ON orders.user_id = users.id";
$params = [];

// Add search condition
if ($search) {
    $query .= " WHERE (users.username LIKE ? OR orders.id LIKE ?)";
    $countQuery .= " WHERE (users.username LIKE ? OR orders.id LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Add status filter
if ($status) {
    $query .= $search ? " AND" : " WHERE";
    $countQuery .= $search ? " AND" : " WHERE";
    $query .= " orders.status = ?";
    $countQuery .= " orders.status = ?";
    $params[] = $status;
}

// Add sorting
$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'created_at';
$sortOrder = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'ASC' : 'DESC';
$query .= " ORDER BY orders.$sortColumn $sortOrder";

// Add pagination
$query .= " LIMIT $perPage OFFSET $offset";

// Execute queries
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll();

$stmt = $pdo->prepare($countQuery);
$stmt->execute($params);
$totalOrders = $stmt->fetchColumn();

$totalPages = ceil($totalOrders / $perPage);

// Fetch order statuses for filter
$statusesQuery = "SELECT DISTINCT status FROM orders";
$statuses = $pdo->query($statusesQuery)->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
        }
        .sidebar-sticky {
            position: relative;
            top: 0;
            height: calc(100vh - 48px);
            padding-top: .5rem;
            overflow-x: hidden;
            overflow-y: auto;
        }
        .navbar-brand {
            padding-top: .75rem;
            padding-bottom: .75rem;
        }
        .navbar .navbar-toggler {
            top: .25rem;
            right: 1rem;
        }
        .order-details {
            display: none;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#">Admin Dashboard</a>
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-nav">
            <div class="nav-item text-nowrap">
                <a class="nav-link px-3" href="login.php">Sign out</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="Orders.php">
                                <i class="fas fa-shopping-cart"></i> Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-box"></i> Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-users"></i> Customers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-chart-bar"></i> Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Orders</h1>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <form action="" method="GET" class="d-flex">
                            <input type="text" name="search" class="form-control me-2" placeholder="Search orders..." value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form action="" method="GET" class="d-flex justify-content-end">
                            <select name="status" class="form-select me-2" style="width: auto;">
                                <option value="">All Statuses</option>
                                <?php foreach ($statuses as $statusOption): ?>
                                    <option value="<?php echo $statusOption; ?>" <?php echo $status === $statusOption ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($statusOption); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-secondary">Filter</button>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>
                                    <a href="?sort=id&order=<?php echo $sortColumn === 'id' && $sortOrder === 'DESC' ? 'asc' : 'desc'; ?><?php echo $search ? "&search=$search" : ''; ?><?php echo $status ? "&status=$status" : ''; ?>">
                                        Order ID
                                        <?php if ($sortColumn === 'id'): ?>
                                            <i class="fas fa-sort-<?php echo $sortOrder === 'ASC' ? 'up' : 'down'; ?>"></i>
                                        <?php endif; ?>
                                    </a>
                                </th>
                                <th>User</th>
                                <th>
                                    <a href="?sort=total_amount&order=<?php echo $sortColumn === 'total_amount' && $sortOrder === 'DESC' ? 'asc' : 'desc'; ?><?php echo $search ? "&search=$search" : ''; ?><?php echo $status ? "&status=$status" : ''; ?>">
                                        Total Amount
                                        <?php if ($sortColumn === 'total_amount'): ?>
                                            <i class="fas fa-sort-<?php echo $sortOrder === 'ASC' ? 'up' : 'down'; ?>"></i>
                                        <?php endif; ?>
                                    </a>
                                </th>
                                <th>
                                    <a href="?sort=status&order=<?php echo $sortColumn === 'status' && $sortOrder === 'DESC' ? 'asc' : 'desc'; ?><?php echo $search ? "&search=$search" : ''; ?><?php echo $status ? "&status=$status" : ''; ?>">
                                        Status
                                        <?php if ($sortColumn === 'status'): ?>
                                            <i class="fas fa-sort-<?php echo $sortOrder === 'ASC' ? 'up' : 'down'; ?>"></i>
                                        <?php endif; ?>
                                    </a>
                                </th>
                                <th>
                                    <a href="?sort=created_at&order=<?php echo $sortColumn === 'created_at' && $sortOrder === 'DESC' ? 'asc' : 'desc'; ?><?php echo $search ? "&search=$search" : ''; ?><?php echo $status ? "&status=$status" : ''; ?>">
                                        Date
                                        <?php if ($sortColumn === 'created_at'): ?>
                                            <i class="fas fa-sort-<?php echo $sortOrder === 'ASC' ? 'up' : 'down'; ?>"></i>
                                        <?php endif; ?>
                                    </a>
                                </th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['username']); ?></td>
                                <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $order['status'] === 'completed' ? 'success' : ($order['status'] === 'cancelled' ? 'danger' : 'warning'); ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('Y-m-d H:i', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info view-details" data-order-id="<?php echo $order['id']; ?>">View</button>
                                    <a href="#" class="btn btn-sm btn-primary">Edit</a>
                                </td>
                            </tr>
                            <tr class="order-details" id="order-<?php echo $order['id']; ?>">
                                <td colspan="6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Order Details</h5>
                                            <p><strong>Order ID:</strong> <?php echo $order['id']; ?></p>
                                            <p><strong>User:</strong> <?php echo htmlspecialchars($order['username']); ?></p>
                                            <p><strong>Total Amount:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>
                                            <p><strong>Status:</strong> <?php echo ucfirst($order['status']); ?></p>
                                            <p><strong>Date:</strong> <?php echo date('Y-m-d H:i', strtotime($order['created_at'])); ?></p>
                                            <!-- Add more order details here -->
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo $page === $i ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?><?php echo $search ? "&search=$search" : ''; ?><?php echo $status ? "&status=$status" : ''; ?><?php echo $sortColumn ? "&sort=$sortColumn" : ''; ?><?php echo $sortOrder ? "&order=$sortOrder" : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.view-details').on('click', function() {
                var orderId = $(this).data('order-id');
                $('#order-' + orderId).toggle();
            });
        });
    </script>
</body>
</html>