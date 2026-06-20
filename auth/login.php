<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: ../pages/dashboard.php");
    exit();
}

include('../config/db.php');

$message = "";

if (isset($_POST['email'])) {

    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $message = "Please enter both email and password.";
    } else {

        $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password'])) {

            // Set ALL session variables (for both old and new pages)
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_name']  = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role']  = $user['role'];

            // For admin/dashboard.php compatibility
            $_SESSION['user']       = ['name' => $user['name'], 'email' => $user['email']];
            $_SESSION['role']       = $user['role'];

            if ($user['role'] === 'admin') {
                $_SESSION['admin'] = true;
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../pages/dashboard.php");
            }
            exit();

        } else {
            $message = "Invalid email or password. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login – FurnishHub</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #5c3d1e, #c9a96e);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
        }
        .card {
            background: white; padding: 40px; border-radius: 15px;
            width: 380px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        h2 { color: #5c3d1e; margin-bottom: 5px; }
        p.sub { color: #888; margin-top: 0; font-size: 14px; }
        label { display: block; font-weight: bold; color: #5c3d1e; margin-bottom: 4px; }
        input {
            width: 100%; padding: 11px; border: 1px solid #c9a96e;
            border-radius: 6px; font-size: 14px; margin-bottom: 15px;
        }
        button {
            width: 100%; background: #5c3d1e; color: white;
            padding: 13px; border: none; border-radius: 6px;
            font-size: 15px; cursor: pointer;
        }
        button:hover { background: #c9a96e; }
        .error { background: #f8d7da; color: #721c24;
                 padding: 10px 14px; border-radius: 6px; margin-bottom: 15px; font-size: 14px; }
        .footer { text-align: center; margin-top: 20px; font-size: 14px; color: #666; }
        .footer a { color: #5c3d1e; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
<div class="card">
    <h2>🛋️ FurnishHub</h2>
    <p class="sub">Sign in to your account</p>

    <?php if ($message): ?>
        <div class="error"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Email Address</label>
        <input type="email" name="email" placeholder="jane@example.com" autofocus
               value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">

        <label>Password</label>
        <input type="password" name="password" placeholder="Your password">

        <button type="submit">Login</button>
    </form>

    <div class="footer">
        Don't have an account? <a href="register.php">Register</a>
    </div>
</div>
</body>
</html>