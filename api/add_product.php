<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category'];

    // Validasi data
    if (empty($name) || empty($price) || empty($stock)) {
        echo json_encode(["success" => false, "message" => "Please fill in all required fields."]);
        exit;
    }

    // Insert produk ke database
    $sql = "INSERT INTO products (name, description, price, stock, category) VALUES (:name, :description, :price, :stock, :category)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':stock', $stock);
    $stmt->bindParam(':category', $category);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Product added successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error adding product."]);
    }
}
?>