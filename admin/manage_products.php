<?php
include '../includes/header.php';
include '../config/db.php';

if(!isset($_SESSION['user']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

// Handle delete
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
    header("Location: manage_products.php");
    exit();
}

// Handle add product
$error = "";
$success = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $stock = $_POST['stock'];

    if(empty($name) || empty($price)){
        $error = "Name and price are required!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category_id, stock) VALUES (?,?,?,?,?)");
        $stmt->execute([$name, $description, $price, $category_id, $stock]);
        $success = "Product added successfully!";
    }
}

$products = $pdo->query("SELECT p.*, c.name as category_name 
                         FROM products p 
                         LEFT JOIN categories c ON p.category_id = c.id")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>

<div class="manage-page">
    <h2>Manage Products</h2>
    <a href="dashboard.php" class="btn">← Back to Dashboard</a>

    <div class="manage-grid">
        <!-- Add Product Form -->
        <div class="add-form">
            <h3>Add New Product</h3>

            <?php if($error): ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if($success): ?>
                <div class="success-msg"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="name" placeholder="e.g. Modern Sofa" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" placeholder="Product description" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Price (KSh)</label>
                    <input type="number" name="price" placeholder="e.g. 45000" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category_id">
                        <?php foreach($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>">
                                <?php echo $cat['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Stock</label>
                    <input type="number" name="stock" placeholder="e.g. 10">
                </div>
                <button type="submit" class="btn-full">Add Product</button>
            </form>
        </div>

        <!-- Products List -->
        <div class="products-list">
            <h3>All Products (<?php echo count($products); ?>)</h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($products as $p): ?>
                    <tr>
                        <td><?php echo $p['id']; ?></td>
                        <td><?php echo $p['name']; ?></td>
                        <td>KSh <?php echo number_format($p['price'], 2); ?></td>
                        <td><?php echo $p['category_name']; ?></td>
                        <td><?php echo $p['stock']; ?></td>
                        <td>
                            <a href="?delete=<?php echo $p['id']; ?>" 
                               class="btn-delete"
                               onclick="return confirm('Delete this product?')">
                               Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>