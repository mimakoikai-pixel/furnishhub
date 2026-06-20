<?php
session_start();
include('../config/db.php');

$message = "";
$msg_type = "";

if (isset($_POST['email'])) {

    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    if (empty($name)) {
        $message = "Full name is required.";
        $msg_type = "error";
    } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "A valid email is required.";
        $msg_type = "error";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
        $msg_type = "error";
    } elseif ($password !== $confirm) {
        $message = "Passwords do not match.";
        $msg_type = "error";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email=?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "Email already registered. Please login.";
            $msg_type = "error";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashed_password);

            if ($stmt->execute()) {
                $message = "✅ Registration successful! You can now login.";
                $msg_type = "success";
            } else {
                $message = "❌ Registration failed. Try again.";
                $msg_type = "error";
            }
            $stmt->close();
        }
        $check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register – FurnishHub</title>
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
            width: 400px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);
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
        .msg { padding: 10px 14px; border-radius: 6px; margin-bottom: 15px; font-size: 14px; }
        .error  { background: #f8d7da; color: #721c24; }
        .success { background: #d4edda; color: #155724; }
        .footer { text-align: center; margin-top: 20px; font-size: 14px; color: #666; }
        .footer a { color: #5c3d1e; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
<div class="card">
    <h2>🛋️ FurnishHub</h2>
    <p class="sub">Create your account</p>

    <?php if ($message): ?>
        <div class="msg <?= $msg_type ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Full Name</label>
        <input type="text" name="name" placeholder="Jane Mwangi"
               value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">

        <label>Email Address</label>
        <input type="email" name="email" placeholder="jane@example.com"
               value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">

        <label>Password</label>
        <input type="password" name="password" placeholder="Min. 6 characters">

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" placeholder="Re-enter password">

        <button type="submit">Create Account</button>
    </form>

    <div class="footer">
        Already have an account? <a href="login.php">Login</a>
    </div>
</div>
</body>
</html>