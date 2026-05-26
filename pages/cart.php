<?php
include '../includes/header.php';
include '../config/db.php';

// Add to cart
if(isset($_GET['add'])){
    $product_id = $_GET['add'];
    if(!isset($_SESSION['cart'])){
        $_SESSION['cart'] = [];
    }
    if(isset($_SESSION['cart'][$product_id])){
        $_SESSION['cart'][$product_id]++;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }
    header("Location: cart.php");
    exit();
}

// Remove from cart
if(isset($_GET['remove'])){
    $product_id = $_GET['remove'];
    unset($_SESSION['cart'][$product_id]);
    header("Location: cart.php");
    exit();
}

// Get cart items
$cart_items = [];
$total = 0;

if(!empty($_SESSION['cart'])){
    foreach($_SESSION['cart'] as $product_id => $quantity){
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        if($product){
            $product['quantity'] = $quantity;
            $product['subtotal'] = $product['price'] * $quantity;
            $total += $product['subtotal'];
            $cart_items[] = $product;
        }
    }
}
?>

<div class="cart-page">
    <h2>🛒 Your Cart</h2>

    <?php if(empty($cart_items)): ?>
        <div class="empty-cart">
            <p>Your cart is empty!</p>
            <a href="products.php" class="btn">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="cart-grid">
            <div class="cart-items">
                <?php foreach($cart_items as $item): ?>
                <div class="cart-item">
                    <div class="cart-item-img">🛋️</div>
                    <div class="cart-item-info">
                        <h3><?php echo $item['name']; ?></h3>
                        <p>KSh <?php echo number_format($item['price'], 2); ?></p>
                        <p>Quantity: <?php echo $item['quantity']; ?></p>
                        <p>Subtotal: <strong>KSh <?php echo number_format($item['subtotal'], 2); ?></strong></p>
                    </div>
                    <a href="?remove=<?php echo $item['id']; ?>" class="btn-delete">Remove</a>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <h3>Order Summary</h3>
                <div class="summary-row">
                    <span>Items (<?php echo count($cart_items); ?>)</span>
                    <span>KSh <?php echo number_format($total, 2); ?></span>
                </div>
                <div class="summary-row">
                    <span>Delivery</span>
                    <span>KSh 500.00</span>
                </div>
                <div class="summary-total">
                    <span>Total</span>
                    <span>KSh <?php echo number_format($total + 500, 2); ?></span>
                </div>
                <?php if(isset($_SESSION['user'])): ?>
                    <a href="orders.php?checkout=1" class="btn-full">Proceed to Checkout</a>
                <?php else: ?>
                    <a href="../auth/login.php" class="btn-full">Login to Checkout</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>