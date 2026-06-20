<?php
session_start();

// PROTECTED - redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

include('../config/db.php');

$user_id = $_SESSION['user_id'];
$orders = mysqli_query($conn, "SELECT * FROM orders WHERE user_id=$user_id ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Dashboard – FurnishHub</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f0eb; margin: 0; }
        .topbar {
            background: #5c3d1e; color: white;
            padding: 15px 30px; display: flex;
            justify-content: space-between; align-items: center;
        }
        .topbar h1 { margin: 0; font-size: 22px; }
        .topbar a { color: #c9a96e; text-decoration: none; font-weight: bold; }
        .topbar a:hover { color: white; }
        .container { padding: 30px; max-width: 900px; margin: auto; }
        .welcome {
            background: white; border-radius: 10px;
            padding: 25px; margin-bottom: 25px;
            border-left: 5px solid #c9a96e;
        }
        .welcome h2 { color: #5c3d1e; margin: 0 0 5px 0; }
        .welcome p { color: #666; margin: 0; }
        .nav-links { margin-bottom: 20px; }
        .nav-links a {
            display: inline-block; margin-right: 10px;
            background: #c9a96e; color: white;
            padding: 8px 18px; border-radius: 6px;
            text-decoration: none; font-size: 14px;
        }
        .nav-links a:hover { background: #5c3d1e; }
        .section-title { color: #5c3d1e; font-size: 18px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; }
        th { background: #5c3d1e; color: white; padding: 12px; text-align: left; }
        td { padding: 10px 12px; border-bottom: 1px solid #e8d5b7; }
        tr:hover { background: #fdf6ec; }
        .no-orders { text-align: center; padding: 30px; color: #999; }
    </style>
</head>
<body>

<div class="topbar">
    <h1>🛋️ FurnishHub</h1>
    <div>
        Welcome, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>
        &nbsp;|&nbsp;
        <a href="../auth/logout.php">🚪 Logout</a>
    </div>
</div>

<div class="container">
    <div class="welcome">
        <h2>Hello, <?= htmlspecialchars($_SESSION['user_name']) ?>! 👋</h2>
        <p>Welcome to your FurnishHub dashboard.</p>
    </div>

    <div class="nav-links">
        <a href="../index.php">🏠 Home</a>
        <a href="../pages/products.php">🛋️ Browse Products</a>
        <a href="../pages/cart.php">🛒 My Cart</a>
    </div>

    <div class="section-title">📦 My Orders</div>

    <table>
        <tr>
            <th>Order #</th>
            <th>Date</th>
            <th>Total (KES)</th>
            <th>Status</th>
        </tr>
        <?php
        if (mysqli_num_rows($orders) > 0) {
            while ($order = mysqli_fetch_assoc($orders)) {
                echo "<tr>
                    <td>#" . $order['id'] . "</td>
                    <td>" . date('d M Y', strtotime($order['created_at'])) . "</td>
                    <td>KES " . number_format($order['total'], 2) . "</td>
                    <td>" . htmlspecialchars($order['status']) . "</td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='4' class='no-orders'>No orders yet. <a href='../pages/products.php'>Start shopping!</a></td></tr>";
        }
        ?>
    </table>
</div>
</body>
</html>