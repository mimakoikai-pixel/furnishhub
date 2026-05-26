<?php 
include '../includes/header.php';
include '../config/db.php';
?>

<div class="products-page">
    <h2>Our Furniture Collection</h2>
    <p class="subtitle">Discover our premium furniture & interior pieces</p>

    <div class="products-grid">
        <?php
        $stmt = $pdo->query("SELECT p.*, c.name as category_name 
                            FROM products p 
                            LEFT JOIN categories c ON p.category_id = c.id");
        $products = $stmt->fetchAll();

        if(count($products) > 0):
            foreach($products as $product): ?>
                <div class="product-card">
                    <div class="product-img">🛋️</div>
                    <div class="product-info">
                        <h3><?php echo $product['name']; ?></h3>
                        <p><?php echo $product['description']; ?></p>
                        <span class="price">KSh <?php echo number_format($product['price'], 2); ?></span>
                        <a href="cart.php?add=<?php echo $product['id']; ?>" class="btn-add">Add to Cart</a>
                    </div>
                </div>
        <?php endforeach;
        else: ?>
            <p class="no-products">No products yet. Check back soon!</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>