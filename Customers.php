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
$role = isset($_GET['role']) ? $_GET['role'] : '';

// Base query
$query = "SELECT users.*, roles.name AS role_name FROM users LEFT JOIN roles ON users.role_id = roles.id";
$countQuery = "SELECT COUNT(*) FROM users LEFT JOIN roles ON users.role_id = roles.id";
$params = [];

// Add search condition
if ($search) {
    $query .= " WHERE (users.username LIKE ? OR users.email LIKE ? OR users.full_name LIKE ?)";
    $countQuery .= " WHERE (users.username LIKE ? OR users.email LIKE ? OR users.full_name LIKE ?)";
    $params = array_merge($params, ["%$search%", "%$search%", "%$search%"]);
}

// Add role filter
if ($role) {
    $query .= $search ? " AND" : " WHERE";
    $countQuery .= $search ? " AND" : " WHERE";
    $query .= " roles.name = ?";
    $countQuery .= " roles.name = ?";
    $params[] = $role;
}

// Add sorting
$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'username';
$sortOrder = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';
$query .= " ORDER BY users.$sortColumn $sortOrder";

// Add pagination
$query .= " LIMIT $perPage OFFSET $offset";

// Execute queries
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$customers = $stmt->fetchAll();

$stmt = $pdo->prepare($countQuery);
$stmt->execute($params);
$totalCustomers = $stmt->fetchColumn();

$totalPages = ceil($totalCustomers / $perPage);

// Fetch roles for filter
$rolesQuery = "SELECT DISTINCT name FROM roles ORDER BY name";
$roles = $pdo->query($rolesQuery)->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers - Admin Dashboard</title>
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
        .customer-avatar {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
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
                            <a class="nav-link" href="Orders.php">
                                <i class="fas fa-shopping-cart"></i> Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="Products.php">
                                <i class="fas fa-box"></i> Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="Customers.php">
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
                    <h1 class="h2">Customers</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                            <i class="fas fa-user-plus"></i> Add New Customer
                        </button>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <form action="" method="GET" class="d-flex">
                            <input type="text" name="search" class="form-control me-2" placeholder="Search customers..." value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form action="" method="GET" class="d-flex justify-content-end">
                            <select name="role" class="form-select me-2" style="width: auto;">
                                <option value="">All Roles</option>
                                <?php foreach ($roles as $r): ?>
                                    <option value="<?php echo htmlspecialchars($r); ?>" <?php echo $role === $r ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($r); ?>
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
                                <th>Avatar</th>
                                <th>
                                    <a href="?sort=username&order=<?php echo $sortColumn === 'username' && $sortOrder === 'ASC' ? 'desc' : 'asc'; ?><?php echo $search ? "&search=$search" : ''; ?><?php echo $role ? "&role=$role" : ''; ?>">
                                        Username
                                        <?php if ($sortColumn === 'username'): ?>
                                            <i class="fas fa-sort-<?php echo $sortOrder === 'ASC' ? 'down' : 'up'; ?>"></i>
                                        <?php endif; ?>
                                    </a>
                                </th>
                                <th>
                                    <a href="?sort=email&order=<?php echo $sortColumn === 'email' && $sortOrder === 'ASC' ? 'desc' : 'asc'; ?><?php echo $search ? "&search=$search" : ''; ?><?php echo $role ? "&role=$role" : ''; ?>">
                                        Email
                                        <?php if ($sortColumn === 'email'): ?>
                                            <i class="fas fa-sort-<?php echo $sortOrder === 'ASC' ? 'down' : 'up'; ?>"></i>
                                        <?php endif; ?>
                                    </a>
                                </th>
                                <th>
                                    <a href="?sort=full_name&order=<?php echo $sortColumn === 'full_name' && $sortOrder === 'ASC' ? 'desc' : 'asc'; ?><?php echo $search ? "&search=$search" : ''; ?><?php echo $role ? "&role=$role" : ''; ?>">
                                        Full Name
                                        <?php if ($sortColumn === 'full_name'): ?>
                                            <i class="fas fa-sort-<?php echo $sortOrder === 'ASC' ? 'down' : 'up'; ?>"></i>
                                        <?php endif; ?>
                                    </a>
                                </th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td>
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($customer['full_name']); ?>&background=random" alt="<?php echo htmlspecialchars($customer['full_name']); ?>" class="customer-avatar">
                                </td>
                                <td><?php echo htmlspecialchars($customer['username']); ?></td>
                                <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                <td><?php echo htmlspecialchars($customer['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($customer['role_name']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info view-customer" data-bs-toggle="modal" data-bs-target="#viewCustomerModal" data-customer-id="<?php echo $customer['id']; ?>">View</button>
                                    <button class="btn btn-sm btn-primary edit-customer" data-bs-toggle="modal" data-bs-target="#editCustomerModal" data-customer-id="<?php echo $customer['id']; ?>">Edit</button>
                                    <button class="btn btn-sm btn-danger delete-customer" data-customer-id="<?php echo $customer['id']; ?>">Delete</button>
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
                                <a class="page-link" href="?page=<?php echo $i; ?><?php echo $search ? "&search=$search" : ''; ?><?php echo $role ? "&role=$role" : ''; ?><?php echo $sortColumn ? "&sort=$sortColumn" : ''; ?><?php echo $sortOrder ? "&order=$sortOrder" : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </main>
        </div>
    </div>

    <!-- Add Customer Modal -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCustomerModalLabel">Add New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addCustomerForm">
                        <div class="mb-3">
                            <label for="customerUsername" class="form-label">Username</label>
                            <input type="text" class="form-control" id="customerUsername" required>
                        </div>
                        <div class="mb-3">
                            <label for="customerEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="customerEmail" required>
                        </div>
                        <div class="mb-3">
                            <label for="customerFullName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="customerFullName" required>
                        </div>
                        <div class="mb-3">
                            <label for="customerPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="customerPassword" required>
                        </div>
                        <div class="mb-3">
                            <label for="customerRole" class="form-label">Role</label>
                            <select class="form-select" id="customerRole" required>
                                <?php foreach ($roles as $r): ?>
                                    <option value="<?php echo $r['id']; ?>"><?php echo htmlspecialchars($r['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveNewCustomer">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Customer Modal -->
    <div class="modal fade" id="viewCustomerModal" tabindex="-1" aria-labelledby="viewCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewCustomerModalLabel">Customer Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="viewCustomerDetails">
                    <!-- Customer details will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Customer Modal -->
    <div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCustomerModalLabel">Edit Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCustomerForm">
                        <input type="hidden" id="editCustomerId">
                        <div class="mb-3">
                            <label for="editCustomerUsername" class="form-label">Username</label>
                            <input type="text" class="form-control" id="editCustomerUsername" required>
                        </div>
                        <div class="mb-3">
                            <label for="editCustomerEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editCustomerEmail" required>
                        </div>
                        <div class="mb-3">
                            <label for="editCustomerFullName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="editCustomerFullName" required>
                        </div>
                        <div class="mb-3">
                            <label for="editCustomerRole" class="form-label">Role</label>
                            <select class="form-select" id="editCustomerRole" required>
                                <?php foreach ($roles as $r): ?>
                                    <option value="<?php echo $r['id']; ?>"><?php echo htmlspecialchars($r['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveEditCustomer">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Add new customer
            $('#saveNewCustomer').on('click', function() {
                var formData = {
                    username: $('#customerUsername').val(),
                    email: $('#customerEmail').val(),
                    full_name: $('#customerFullName').val(),
                    password: $('#customerPassword').val(),
                    role_id: $('#customerRole').val()
                };

                $.ajax({
                    url: 'api/add_customer.php',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        alert('Customer added successfully');
                        location.reload();
                    },
                    error: function() {
                        alert('Error adding customer');
                    }
                });
            });

            // View customer details
            $('.view-customer').on('click', function() {
                var customerId = $(this).data('customer-id');
                $.ajax({
                    url: 'api/get_customer.php',
                    type: 'GET',
                    data: { id: customerId },
                    success: function(response) {
                        $('#viewCustomerDetails').html(response);
                    },
                    error: function() {
                        alert('Error fetching customer details');
                    }
                });
            });

            // Load customer data for editing
            $('.edit-customer').on('click', function() {
                var customerId = $(this).data('customer-id');
                $.ajax({
                    url: 'api/get_customer.php',
                    type: 'GET',
                    data: { id: customerId },
                    success: function(response) {
                        var customer = JSON.parse(response);
                        $('#editCustomerId').val(customer.id);
                        $('#editCustomerUsername').val(customer.username);
                        $('#editCustomerEmail').val(customer.email);
                        $('#editCustomerFullName').val(customer.full_name);
                        $('#editCustomerRole').val(customer.role_id);
                    },
                    error: function() {
                        alert('Error fetching customer data');
                    }
                });
            });

            // Save edited customer
            $('#saveEditCustomer').on('click', function() {
                var formData = {
                    id: $('#editCustomerId').val(),
                    username: $('#editCustomerUsername').val(),
                    email: $('#editCustomerEmail').val(),
                    full_name: $('#editCustomerFullName').val(),
                    role_id: $('#editCustomerRole').val()
                };

                $.ajax({
                    url: 'api/update_customer.php',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        alert('Customer updated successfully');
                        location.reload();
                    },
                    error: function() {
                        alert('Error updating customer');
                    }
                });
            });

            // Delete customer
            $('.delete-customer').on('click', function() {
                if (confirm('Are you sure you want to delete this customer?')) {
                    var customerId = $(this).data('customer-id');
                    $.ajax({
                        url: 'api/delete_customer.php',
                        type: 'POST',
                        data: { id: customerId },
                        success: function(response) {
                            alert('Customer deleted successfully');
                            location.reload();
                        },
                        error: function() {
                            alert('Error deleting customer');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>