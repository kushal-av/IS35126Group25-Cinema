<?php
// auth/google-callback.php
require_once '../includes/security.php';
session_start();

require_once '../config/db.php';
require_once '../vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('1094750969291-k8itgauro8lav6vs58h4i5hb3aji9fn5.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('https://is35126group25-cinema.onrender.com/auth/google-callback.php');

if (!isset($_GET['code'])) {
    die("Google Login Failed - No Code Received");
}

try {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (isset($token['error'])) {
        die("Google Token Error: " . htmlspecialchars($token['error_description'] ?? $token['error']));
    }

    $client->setAccessToken($token);

    $google_service = new Google_Service_Oauth2($client);
    $google_user = $google_service->userinfo->get();

    $email = $google_user->email;
    $full_name = $google_user->name;
    $google_id = $google_user->id;

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $dummy_password = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users 
            (full_name, email, google_id, role, password_hash) 
            VALUES (?, ?, ?, 'customer', ?)");
        $stmt->execute([$full_name, $email, $google_id, $dummy_password]);

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET google_id = ? WHERE id = ?");
        $stmt->execute([$google_id, $user['id']]);
    }

    session_regenerate_id(true);
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

} catch (Exception $e) {
    die("Google Login Error: " . htmlspecialchars($e->getMessage()));
}
?>