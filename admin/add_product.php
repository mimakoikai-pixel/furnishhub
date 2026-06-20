<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit();
}
include('../config/db.php');

$message = "";

if (isset($_POST['name'])) {

    if (empty($_POST['name'])) {
        $message = "Product name is required.";
    } elseif (empty($_POST['price']) || !is_numeric($_POST['price'])) {
        $message = "Valid price is required.";
    } elseif (empty($_POST['category_id'])) {
        $message = "Category is required.";
    } else {

        $name        = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price       = floatval($_POST['price']);
        $category_id = intval($_POST['category_id']);
        $image       = trim($_POST['image']);
        $stock       = intval($_POST['stock'] ?? 0);

        $stmt = $conn->prepare(
            "INSERT INTO products (name, description, price, category_id, image, stock)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssdiis", $name, $description, $price, $category_id, $image, $stock);

        if ($stmt->execute()) {
            $message = "✅ Product added successfully!";
        } else {
            $message = "❌ Error: " . $conn->error;
        }
        $stmt->close();
    }
}

// Load categories from database
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product – FurnishHub Admin</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f0eb; padding: 30px; }
        h2 { color: #5c3d1e; }
        form { background: white; padding: 25px; border-radius: 10px; max-width: 500px; }
        input, textarea, select {
            width: 100%; padding: 10px; margin: 8px 0 16px 0;
            border: 1px solid #c9a96e; border-radius: 6px;
            font-size: 14px; box-sizing: border-box;
        }
        button {
            background: #c9a96e; color: white; padding: 12px 25px;
            border: none; border-radius: 6px; cursor: pointer; font-size: 15px;
        }
        button:hover { background: #5c3d1e; }
        .message { padding: 10px; border-radius: 6px; margin-bottom: 15px;
                   background: #fff3cd; color: #856404; }
        .success { background: #d4edda; color: #155724; }
        label { font-weight: bold; color: #5c3d1e; }
    </style>
</head>
<body>
    <h2>➕ Add New Product</h2>

    <?php if ($message): ?>
        <div class="message <?= strpos($message, '✅') !== false ? 'success' : '' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <label>Product Name</label>
        <input type="text" name="name" placeholder="e.g. Coffee Table"
               value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">

        <label>Description</label>
        <textarea name="description" rows="3" placeholder="Describe the product..."><?=
            isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''
        ?></textarea>

        <label>Price (KES)</label>
        <input type="number" name="price" placeholder="e.g. 15000" step="0.01"
               value="<?= isset($_POST['price']) ? htmlspecialchars($_POST['price']) : '' ?>">

        <label>Category</label>
        <select name="category_id">
            <option value="">-- Select Category --</option>
            <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                <option value="<?= $cat['id'] ?>"
                    <?= (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Stock Quantity</label>
        <input type="number" name="stock" placeholder="e.g. 10"
               value="<?= isset($_POST['stock']) ? htmlspecialchars($_POST['stock']) : '' ?>">

        <label>Image URL</label>
        <input type="text" name="image" placeholder="https://..."
               value="<?= isset($_POST['image']) ? htmlspecialchars($_POST['image']) : '' ?>">

        <button type="submit">Save Product</button>
    </form>

    <br><a href="manage_products.php" style="color:#5c3d1e;">← Back to Products</a>
</body>
</html>