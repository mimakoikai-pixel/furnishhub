<?php 
include '../includes/header.php';
include '../config/db.php';

$error = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if(empty($email) || empty($password)){
        $error = "All fields are required!";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if($user && password_verify($password, $user['password'])){
            $_SESSION['user'] = $user;
            $_SESSION['role'] = $user['role'];
            
            if($user['role'] == 'admin'){
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../pages/products.php");
            }
            exit();
        } else {
            $error = "Invalid email or password!";
        }
    }
}
?>

<div class="auth-container">
    <h2>Login to FurnishHub</h2>

    <?php if($error): ?>
        <div class="error-msg"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" 
            placeholder="Enter your email" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" 
            placeholder="Enter your password" required>
        </div>

        <button type="submit" class="btn-full">Login</button>

        <p class="auth-link">Don't have an account? 
            <a href="register.php">Register here</a>
        </p>
    </form>
</div>

<?php include '../includes/footer.php'; ?>