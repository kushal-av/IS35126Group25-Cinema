<?php
// auth/2fa_verify.php
require_once '../includes/security.php';
session_start();
require_once '../config/db.php';
require_once '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['temp_user'])) {
    header("Location: /auth/login.php");
    exit;
}

$message = '';

if ($_POST) {
    $entered_otp = $_POST['otp'];

    if ($entered_otp == $_SESSION['otp']) {
        $_SESSION['user'] = $_SESSION['temp_user'];
        unset($_SESSION['temp_user']);
        unset($_SESSION['otp']);

        $role = $_SESSION['user']['role'];
        if ($role == 'admin') header("Location: /admin/dashboard.php");
        elseif ($role == 'staff') header("Location: /staff/dashboard.php");
        else header("Location: /customer/dashboard.php");
        exit;
    } else {
        $message = "❌ Incorrect OTP!";
    }
}

// Resend OTP
if (isset($_GET['resend'])) {
    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp;

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'kavishraj919@gmail.com';           // ← Change
        $mail->Password = 'abcdefghijklmnopqrstuv';              // ← Change
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('kavishraj919@gmail.com', 'Cinema Group 25');
        $mail->addAddress($_SESSION['temp_user']['email']);
        $mail->isHTML(true);
        $mail->Subject = 'Your 2FA OTP Code';
        $mail->Body = "Your OTP for Cinema Booking is: <b>$otp</b><br>Valid for 5 minutes.";

        $mail->send();
        $message = "✅ New OTP sent to your email!";
    } catch (Exception $e) {
        $message = "Failed to send email: " . $mail->ErrorInfo;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2FA Verification - Group 25</title>
    <style>
        body { font-family: Arial; text-align: center; margin-top: 100px; }
        input { padding: 12px; width: 300px; font-size: 18px; }
        button { padding: 12px 30px; font-size: 16px; }
    </style>
</head>
<body>
    <h2>Two-Factor Authentication</h2>
    <p>Enter the OTP sent to your email</p>
    
    <p style="color:green;"><?php echo $message; ?></p>

    <form method="POST">
        <input type="text" name="otp" placeholder="Enter 6-digit OTP" maxlength="6" required><br><br>
        <button type="submit">Verify OTP</button>
    </form>

    <p><a href="?resend=1">Resend OTP</a></p>
    <p><a href="/auth/login.php">Back to Login</a></p>
</body>
</html>