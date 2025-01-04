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
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Base query
$query = "SELECT * FROM products";
$countQuery = "SELECT COUNT(*) FROM products";
$params = [];

// Add search condition
if ($search) {
    $query .= " WHERE (name LIKE ? OR description LIKE ?)";
    $countQuery .= " WHERE (name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Add category filter
if ($category) {
    $query .= $search ? " AND" : " WHERE";
    $countQuery .= $search ? " AND" : " WHERE";
    $query .= " category = ?";
    $countQuery .= " category = ?";
    $params[] = $category;
}

// Add sorting
$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'name';
$sortOrder = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';
$query .= " ORDER BY $sortColumn $sortOrder";

// Add pagination
$query .= " LIMIT $perPage OFFSET $offset";

// Execute queries
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

$stmt = $pdo->prepare($countQuery);
$stmt->execute($params);
$totalProducts = $stmt->fetchColumn();

$totalPages = ceil($totalProducts / $perPage);

// Fetch unique categories for filter
$categoriesQuery = "SELECT DISTINCT category FROM products ORDER BY category";
$categories = $pdo->query($categoriesQuery)->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Admin Dashboard</title>
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
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
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
                            <a class="nav-link active" aria-current="page" href="Products.php">
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
                    <h1 class="h2">Products</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                            <i class="fas fa-plus"></i> Add New Product
                        </button>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <form action="" method="GET" class="d-flex">
                            <input type="text" name="search" class="form-control me-2" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form action="" method="GET" class="d-flex justify-content-end">
                            <select name="category" class="form-select me-2" style="width: auto;">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $category === $cat ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat); ?>
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
                                    <a href="?sort=name&order=<?php echo $sortColumn === 'name' && $sortOrder === 'ASC' ? 'desc' : 'asc'; ?><?php echo $search ? "&search=$search" : ''; ?><?php echo $category ? "&category=$category" : ''; ?>">
                                        Name
                                        <?php if ($sortColumn === 'name'): ?>
                                            <i class="fas fa-sort-<?php echo $sortOrder === 'ASC' ? 'down' : 'up'; ?>"></i>
                                        <?php endif; ?>
                                    </a>
                                </th>
                                <th>Category</th>
                                <th>
                                    <a href="?sort=price&order=<?php echo $sortColumn === 'price' && $sortOrder === 'ASC' ? 'desc' : 'asc'; ?><?php echo $search ? "&search=$search" : ''; ?><?php echo $category ? "&category=$category" : ''; ?>">
                                        Price
                                        <?php if ($sortColumn === 'price'): ?>
                                            <i class="fas fa-sort-<?php echo $sortOrder === 'ASC' ? 'down' : 'up'; ?>"></i>
                                        <?php endif; ?>
                                    </a>
                                </th>
                                <th>
                                    <a href="?sort=stock&order=<?php echo $sortColumn === 'stock' && $sortOrder === 'ASC' ? 'desc' : 'asc'; ?><?php echo $search ? "&search=$search" : ''; ?><?php echo $category ? "&category=$category" : ''; ?>">
                                        Stock
                                        <?php if ($sortColumn === 'stock'): ?>
                                            <i class="fas fa-sort-<?php echo $sortOrder === 'ASC' ? 'down' : 'up'; ?>"></i>
                                        <?php endif; ?>
                                    </a>
                                </th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['category']); ?></td>
                                <td>$<?php echo number_format($product['price'], 2); ?></td>
                                <td><?php echo $product['stock']; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info view-product" data-bs-toggle="modal" data-bs-target="#viewProductModal" data-product-id="<?php echo $product['id']; ?>">View</button>
                                    <a href="editProduct.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">Edit</a>                                    
                                    <button class="btn btn-sm btn-danger delete-product" data-product-id="<?php echo $product['id']; ?>">Delete</button>
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
                                <a class="page-link" href="?page=<?php echo $i; ?><?php echo $search ? "&search=$search" : ''; ?><?php echo $category ? "&category=$category" : ''; ?><?php echo $sortColumn ? "&sort=$sortColumn" : ''; ?><?php echo $sortOrder ? "&order=$sortOrder" : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </main>
        </div>
    </div>

   <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addProductForm">
                        <div class="mb-3">
                            <label for="productName" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="productName" required>
                        </div>
                        <div class="mb-3">
                            <label for="productCategory" class="form-label">Category</label>
                            <input type="text" class="form-control" id="productCategory" required>
                        </div>
                        <div class="mb-3">
                            <label for="productPrice" class="form-label">Price</label>
                            <input type="number" class="form-control" id="productPrice" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="productStock" class="form-label">Stock</label>
                            <input type="number" class="form-control" id="productStock" required>
                        </div>
                        <div class="mb-3">
                            <label for="productDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="productDescription" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveNewProduct">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Product Modal -->
    <div class="modal fade" id="viewProductModal" tabindex="-1" aria-labelledby="viewProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewProductModalLabel">Product Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="viewProductDetails">
                    <!-- Product details will be loaded here -->
                </div>
            </div>
        </div</div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editProductForm">
                        <input type="hidden" id="editProductId">
                        <div class="mb-3">
                            <label for="editProductName" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="editProductName" required>
                        </div>
                        <div class="mb-3">
                            <label for="editProductCategory" class="form-label">Category</label>
                            <input type="text" class="form-control" id="editProductCategory" required>
                        </div>
                        <div class="mb-3">
                            <label for="editProductPrice" class="form-label">Price</label>
                            <input type="number" class="form-control" id="editProductPrice" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="editProductStock" class="form-label">Stock</label>
                            <input type="number" class="form-control" id="editProductStock" required>
                        </div>
                        <div class="mb-3">
                            <label for="editProductDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="editProductDescription" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveEditProduct">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
            $(document).ready(function() {
            // Add new product
            $('#saveNewProduct').on('click', function() {
                var formData = {
                    name: $('#productName').val(),
                    category: $('#productCategory').val(),
                    price: $('#productPrice').val(),
                    stock: $('#productStock').val(),
                    description: $('#productDescription').val()
                };

                $.ajax({
                    url: 'api/add_product.php',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        var jsonResponse = JSON.parse(response);
                        if (jsonResponse.success) {
                            alert(jsonResponse.message);
                            // Refresh the page or append new product to the table
                            location.reload(); // refresh to see the new product
                        } else {
                            alert(jsonResponse.message);
                        }
                    },
                    error: function() {
                        alert('Error adding product');
                    }
                });
            });

        // View product details
            $('.view-product').on('click', function() {
                var productId = $(this).data('product-id');
                $.ajax({
                    url: 'api/get_product.php',
                    type: 'GET',
                    data: { id: productId },
                    success: function(response) {
                        var product = JSON.parse(response);
                        if (product) {
                            $('#viewProductDetails').html(`
                                <strong>Name:</strong> ${product.name}<br>
                                <strong>Category:</strong> ${product.category}<br>
                                <strong>Price:</strong> $${parseFloat(product.price).toFixed(2)}<br>
                                <strong>Stock:</strong> ${product.stock}<br>
                                <strong>Description:</strong> ${product.description}
                            `);
                        } else {
                            alert('Error fetching product details.');
                        }
                    },
                    error: function() {
                        alert('Error fetching product details');
                    }
                });
            });
            
            // Load product data for editing
            $('.edit-product').on('click', function() {
                var productId = $(this).data('product-id');
                $.ajax({
                    url: 'api/get_product.php',
                    type: 'GET',
                    data: { id: productId },
                    success: function(response) {
                        var product = JSON.parse(response);
                        $('#editProductId').val(product.id);
                        $('#editProductName').val(product.name);
                        $('#editProductCategory').val(product.category);
                        $('#editProductPrice').val(product.price);
                        $('#editProductStock').val(product.stock);
                        $('#editProductDescription').val(product.description);
                    },
                    error: function() {
                        alert('Error fetching product data');
                    }
                });
            });

            $('#saveEditProduct').on('click', function() {
            var formData = {
                id: $('input[name="id"]').val(),
                name: $('input[name="name"]').val(),
                category: $('input[name="category"]').val(),
                price: $('input[name="price"]').val(),
                stock: $('input[name="stock"]').val(),
                description: $('textarea[name="description"]').val()
            };

            $.ajax({
                url: 'api/update_product.php',
                type: 'POST',
                data: formData,
                success: function(response) {
                    var jsonResponse = JSON.parse(response);
                    alert(jsonResponse.message);
                    if (jsonResponse.success) {
                        location.reload(); // Refresh halaman untuk melihat perubahan
                    }
                },
                error: function() {
                    alert('Error updating product');
                }
            });
        });

            // Delete product
            $('.delete-product').on('click', function() {
                if (confirm('Are you sure you want to delete this product?')) {
                    var productId = $(this).data('product-id');
                    $.ajax({
                        url: 'api/delete_product.php',
                        type: 'POST',
                        data: { id: productId },
                        success: function(response) {
                            var jsonResponse = JSON.parse(response);
                            if (jsonResponse.success) {
                                alert(jsonResponse.message);
                                location.reload(); // Refresh to see the updated product list
                            } else {
                                alert(jsonResponse.message);
                            }
                        },
                        error: function() {
                            alert('Error deleting product');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>