<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FurnishHub</title>
    <link rel="stylesheet" href="/furnishhub/assets/css/style.css">
</head>
<body>
<nav class="navbar">
    <div class="logo">🛋️ FurnishHub</div>
    <ul class="nav-links">
        <li><a href="/furnishhub/index.php">Home</a></li>
        <li><a href="/furnishhub/pages/products.php">Products</a></li>
        <li><a href="/furnishhub/pages/cart.php">Cart</a></li>
        <?php if(isset($_SESSION['user'])): ?>
            <li><a href="/furnishhub/auth/logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="/furnishhub/auth/login.php">Login</a></li>
        <?php endif; ?>
    </ul>
</nav>