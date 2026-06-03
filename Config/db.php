<?php
// config/db.php - Local MySQL Connection
$host = 'localhost';
$dbname = 'cinema_is351_group25';
$username = 'root';
$password = 'YourStrongPassword123';           // Leave empty if no password set

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch(PDOException $e) {
    die("❌ Database Connection Failed: " . $e->getMessage());
}
?>