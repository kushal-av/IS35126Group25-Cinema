<?php
// auth/login.php

require_once '../includes/security.php';
session_start();
require_once '../config/db.php';

$message = '';

if ($_POST) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $selected_role = $_POST['role'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
    $stmt->execute([$email, $selected_role]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {

        $_SESSION['user'] = $user;

        if ($user['role'] === 'admin') {
            header("Location: /admin/dashboard.php");
            exit;
        } elseif ($user['role'] === 'staff') {
            header("Location: /staff/dashboard.php");
            exit;
        } else {
            header("Location: /customer/dashboard.php");
            exit;
        }

    } else {
        $message = "❌ Incorrect email, password, or role selected!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - CINEMA 25</title>

<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: Arial, sans-serif;
    color: white;
    min-height: 100vh;
    background: linear-gradient(rgba(10,0,0,0.85), rgba(0,0,0,0.9)), #000;
}

.container {
    max-width: 420px;
    margin: 120px auto;
    padding: 40px 30px;
    background: rgba(20,0,0,0.85);
    border-radius: 16px;
    border: 1px solid #e50914;
    box-shadow: 0 0 40px rgba(229,9,20,0.4);
}

h2 {
    text-align: center;
    margin-bottom: 30px;
    font-size: 2.2rem;
}

input, select {
    width: 100%;
    padding: 14px;
    margin: 12px 0;
    background: #111;
    border: 1px solid #444;
    color: white;
    border-radius: 8px;
}

button {
    width: 100%;
    padding: 16px;
    background: #e50914;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: bold;
    cursor: pointer;
    margin-top: 10px;
}

.google-btn {
    display: block;
    text-align: center;
    padding: 14px;
    background: #db4437;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    margin: 15px 0;
}
</style>
</head>

<body>

<div class="container">
    <h2>Login to CINEMA 25</h2>

    <?php if ($message): ?>
        <p style="color:#ffd700; text-align:center; margin-bottom:15px;">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email Address" required>

        <input type="password" name="password" placeholder="Password" required>

        <select name="role" required>
            <option value="">Select Role</option>
            <option value="customer">Customer</option>
            <option value="staff">Staff</option>
            <option value="admin">Admin</option>
        </select>

        <button type="submit">Login</button>
    </form>

    <p style="text-align:center; margin:20px 0;">OR</p>

    <a href="/auth/google-login.php" class="google-btn">🔵 Login with Google</a>

    <p style="text-align:center;">
        <a href="/auth/register.php" style="color:#ffd700;">
            Don't have an account? Register
        </a>
    </p>
</div>

</body>
</html>