<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit();
}
include('../config/db.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products – FurnishHub Admin</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f0eb; padding: 30px; }
        h2 { color: #5c3d1e; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; }
        th { background: #5c3d1e; color: white; padding: 12px; text-align: left; }
        td { padding: 10px 12px; border-bottom: 1px solid #e8d5b7; }
        tr:hover { background: #fdf6ec; }
        img { width: 60px; height: 60px; object-fit: cover; border-radius: 6px; }
        .btn { padding: 6px 12px; border-radius: 5px; text-decoration: none; font-size: 13px; }
        .edit { background: #c9a96e; color: white; }
        .delete { background: #dc3545; color: white; }
        .btn:hover { opacity: 0.85; }
        .add-btn {
            display: inline-block; margin-bottom: 20px;
            background: #5c3d1e; color: white;
            padding: 10px 20px; border-radius: 6px; text-decoration: none;
        }
        .topbar {
            background: #5c3d1e; color: white;
            padding: 15px 30px; display: flex;
            justify-content: space-between; align-items: center;
            margin: -30px -30px 30px -30px;
        }
        .topbar h1 { margin: 0; font-size: 20px; }
        .topbar a { color: #c9a96e; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="topbar">
    <h1>🛋️ FurnishHub Admin</h1>
    <div>
        <a href="dashboard.php">Dashboard</a> &nbsp;|&nbsp;
        <a href="../auth/logout.php">🚪 Logout</a>
    </div>
</div>

<h2>🛋️ Manage Products</h2>
<a href="add_product.php" class="add-btn">➕ Add New Product</a>

<table>
    <tr>
        <th>Image</th>
        <th>Name</th>
        <th>Category</th>
        <th>Price (KES)</th>
        <th>Stock</th>
        <th>Actions</th>
    </tr>

    <?php
    // JOIN products with categories to get category name
    $result = mysqli_query($conn,
        "SELECT p.*, c.name AS category_name
         FROM products p
         LEFT JOIN categories c ON p.category_id = c.id
         ORDER BY p.id DESC"
    );

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td><img src='" . htmlspecialchars($row['image']) . "' alt='product'></td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['category_name'] ?? 'Uncategorized') . "</td>";
            echo "<td>KES " . number_format($row['price'], 2) . "</td>";
            echo "<td>" . ($row['stock'] ?? 0) . "</td>";
            echo "<td>
                    <a href='edit_product.php?id=" . $row['id'] . "' class='btn edit'>✏️ Edit</a>
                    &nbsp;
                    <a href='delete_product.php?id=" . $row['id'] . "'
                       class='btn delete'
                       onclick=\"return confirm('Delete this product?')\">🗑️ Delete</a>
                  </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6' style='text-align:center;padding:20px;'>No products found.</td></tr>";
    }
    ?>
</table>

</body>
</html>