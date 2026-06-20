<?php
include('config/db.php');

$hash = password_hash('password', PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE users SET password=? WHERE email='admin@furnishhub.com'");
$stmt->bind_param("s", $hash);
$stmt->execute();

echo "Done! Admin password updated. Hash: " . $hash;
?>