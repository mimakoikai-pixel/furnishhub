<?php 
include '../includes/header.php';
include '../config/db.php';

$error = "";
$success = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if(empty($name) || empty($email) || empty($password)){
        $error = "All fields are required!";
    } elseif($password !== $confirm){
        $error = "Passwords do not match!";
    } elseif(strlen($password) < 6){
        $error = "Password must be at least 6 characters!";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if($stmt->fetch()){
            $error = "Email already registered!";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hashed]);
            $success = "Account created! You can now login.";
        }
    }
}
?>

<div class="auth-container">
    <h2>Create an Account</h2>

    <?php if($error): ?>
        <div class="error-msg"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if($success): ?>
        <div class="success-msg"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" 
            placeholder="Enter your full name" required>
        </div>

        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" 
            placeholder="Enter your email" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" 
            placeholder="Min 6 characters" required>
        </div>

        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" 
            placeholder="Repeat your password" required>
        </div>

        <button type="submit" class="btn-full">Register</button>

        <p class="auth-link">Already have an account? 
            <a href="login.php">Login here</a>
        </p>
    </form>
</div>

<?php include '../includes/footer.php'; ?>