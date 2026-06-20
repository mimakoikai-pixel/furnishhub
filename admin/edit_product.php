<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit();
}
include('../config/db.php');

$message = "";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_products.php");
    exit();
}

$id = intval($_GET['id']);
$product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id=$id"));

if (!$product) {
    echo "Product not found.";
    exit();
}

if (isset($_POST['name'])) {
    if (empty($_POST['name'])) {
        $message = "Product name is required.";
    } elseif (!is_numeric($_POST['price'])) {
        $message = "Price must be a number.";
    } else {
        $name        = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price       = floatval($_POST['price']);
        $category_id = intval($_POST['category_id']);
        $image       = trim($_POST['image']);
        $stock       = intval($_POST['stock'] ?? 0);

        $stmt = $conn->prepare(
            "UPDATE products SET name=?, description=?, price=?, category_id=?, image=?, stock=? WHERE id=?"
        );
        $stmt->bind_param("ssdisii", $name, $description, $price, $category_id, $image, $stock, $id);

        if ($stmt->execute()) {
            $message = "✅ Product updated successfully!";
            $product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id=$id"));
        } else {
            $message = "❌ Error: " . $conn->error;
        }
        $stmt->close();
    }
}

// Load categories
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product – FurnishHub</title>
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
                   background: #d4edda; color: #155724; }
        label { font-weight: bold; color: #5c3d1e; }
    </style>
</head>
<body>
    <h2>✏️ Edit Product</h2>

    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Product Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>">

        <label>Description</label>
        <textarea name="description" rows="3"><?= htmlspecialchars($product['description']) ?></textarea>

        <label>Price (KES)</label>
        <input type="number" name="price" step="0.01" value="<?= htmlspecialchars($product['price']) ?>">

        <label>Category</label>
        <select name="category_id">
            <option value="">-- Select Category --</option>
            <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                <option value="<?= $cat['id'] ?>"
                    <?= ($product['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Stock Quantity</label>
        <input type="number" name="stock" value="<?= htmlspecialchars($product['stock'] ?? 0) ?>">

        <label>Image URL</label>
        <input type="text" name="image" value="<?= htmlspecialchars($product['image']) ?>">

        <button type="submit">Update Product</button>
    </form>

    <br><a href="manage_products.php" style="color:#5c3d1e;">← Back to Products</a>
</body>
</html>