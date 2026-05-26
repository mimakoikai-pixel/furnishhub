<?php
include '../includes/header.php';
include '../config/db.php';

// Must be logged in
if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];
$success = "";

// Handle checkout
if(isset($_GET['checkout']) && !empty($_SESSION['cart'])){
    $total = 0;
    foreach($_SESSION['cart'] as $product_id => $quantity){
        $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        if($product){
            $total += $product['price'] * $quantity;
        }
    }
    $total += 500; // delivery

    // Create order
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total) VALUES (?, ?)");
    $stmt->execute([$user_id, $total]);
    $order_id = $pdo->lastInsertId();

    // Save order items
    foreach($_SESSION['cart'] as $product_id => $quantity){
        $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        if($product){
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?,?,?,?)");
            $stmt->execute([$order_id, $product_id, $quantity, $product['price']]);
        }
    }

    // Clear cart
    $_SESSION['cart'] = [];
    $success = "Order placed successfully! Your furniture is on its way! 🛋️";
}

// Get user orders
$orders = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$orders->execute([$user_id]);
$orders = $orders->fetchAll();
?>

<div class="orders-page">
    <h2>📦 My Orders</h2>

    <?php if($success): ?>
        <div class="success-msg" style="margin-bottom:20px;">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <?php if(empty($orders)): ?>
        <div class="empty-cart">
            <p>No orders yet!</p>
            <a href="products.php" class="btn">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="orders-list">
            <?php foreach($orders as $order): ?>
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <h3>Order #<?php echo $order['id']; ?></h3>
                        <p><?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></p>
                    </div>
                    <div class="order-status <?php echo $order['status']; ?>">
                        <?php echo ucfirst($order['status']); ?>
                    </div>
                </div>
                <div class="order-footer">
                    <span>Total: <strong>KSh <?php echo number_format($order['total'], 2); ?></strong></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>