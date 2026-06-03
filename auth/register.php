<?php
// auth/register.php
require_once '../includes/security.php';
session_start();
require_once '../config/db.php';

$message = '';

if ($_POST) {

    $secretKey = getenv('RECAPTCHA_SECRET') ?: 'YOUR_RECAPTCHA_SECRET_KEY';

    $response = $_POST['g-recaptcha-response'] ?? '';

    $verify = file_get_contents(
        "https://www.google.com/recaptcha/api/siteverify?secret=" .
        urlencode($secretKey) .
        "&response=" .
        urlencode($response)
    );

    $result = json_decode($verify, true);

    if (!$result['success']) {
        $message = "❌ Please complete the CAPTCHA verification.";
    } else {
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        if (strlen($password) < 6) {
            $message = "❌ Password must be at least 6 characters!";
        } else {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->rowCount() > 0) {
                $message = "❌ Email already registered!";
            } else {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("
                    INSERT INTO users 
                    (full_name, email, password_hash, role) 
                    VALUES (?, ?, ?, 'customer')
                ");
                $stmt->execute([$full_name, $email, $password_hash]);

                $message = "✅ Registration Successful! <a href='/auth/login.php' style='color:#ffd700;'>Login Now</a>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register - CINEMA 25</title>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: 'Helvetica Neue', Arial, sans-serif;
    color: #fff;
    min-height: 100vh;
    background: #000;
}
.bg {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: linear-gradient(rgba(10,0,0,0.85), rgba(0,0,0,0.9));
    z-index: -2;
}
.container {
    position: relative;
    z-index: 2;
    max-width: 420px;
    margin: 100px auto;
    padding: 40px 30px;
    background: rgba(20,0,0,0.85);
    border-radius: 16px;
    border: 1px solid #e50914;
    box-shadow: 0 0 40px rgba(229,9,20,0.4);
}
h2 { text-align: center; margin-bottom: 30px; font-size: 2.2rem; }
input {
    width: 100%;
    padding: 14px;
    margin: 12px 0;
    background: #111;
    border: 1px solid #444;
    color: white;
    border-radius: 8px;
}
.g-recaptcha {
    margin: 15px 0;
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
    margin-top: 15px;
}
</style>
</head>

<body>
<div class="bg"></div>

<div class="container">
    <h2>Join CINEMA 25</h2>

    <?php if ($message): ?>
        <p style="color:#ffd700; text-align:center; margin-bottom:15px;">
            <?= $message ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="full_name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password (min 6 characters)" required>

        <div class="g-recaptcha" data-sitekey="6Lf6cAotAAAAAHwCrIH0hV6IkiNrO-I-aFqUP9ck"></div>

        <button type="submit">Register</button>
    </form>

    <p style="text-align:center; margin-top:20px;">
        <a href="/auth/login.php" style="color:#ffd700;">Already have an account? Login</a>
    </p>
</div>

</body>
</html>