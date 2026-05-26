<?php
include '../includes/header.php';
include '../config/db.php';

// Protect this page
if(!isset($_SESSION['user']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

// Get counts
$users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$categories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
?>

<div class="dashboard">
    <h2>Admin Dashboard</h2>
    <p class="subtitle">Welcome, <?php echo $_SESSION['user']['name']; ?>!</p>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-info">
                <h3><?php echo $users; ?></h3>
                <p>Total Users</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🛋️</div>
            <div class="stat-info">
                <h3><?php echo $products; ?></h3>
                <p>Total Products</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📦</div>
            <div class="stat-info">
                <h3><?php echo $orders; ?></h3>
                <p>Total Orders</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🏷️</div>
            <div class="stat-info">
                <h3><?php echo $categories; ?></h3>
                <p>Categories</p>
            </div>
        </div>
    </div>

    <div class="admin-actions">
        <h3>Quick Actions</h3>
        <div class="action-buttons">
            <a href="manage_products.php" class="btn">🛋️ Manage Products</a>
            <a href="../pages/products.php" class="btn">🛒 View Shop</a>
            <a href="../auth/logout.php" class="btn btn-danger">🚪 Logout</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>