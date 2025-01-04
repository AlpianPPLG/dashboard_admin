<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Update product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];

    $stmt = $pdo->prepare("UPDATE products SET name = ?, category = ?, price = ?, stock = ?, description = ? WHERE id = ?");
    $success = $stmt->execute([$name, $category, $price, $stock, $description, $id]);

    // Set notification message
    if ($success) {
        echo "<script>alert('Product updated successfully.'); window.location.href='javascript:history.back()';</script>";
    } else {
        echo "<script>alert('Failed to update product.'); window.location.href='javascript:history.back()';</script>";
    }
}

?>
