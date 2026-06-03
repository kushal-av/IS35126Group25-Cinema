<?php
// auth/google-callback.php
require_once '../includes/security.php';
session_start();
require_once '../config/db.php';
require_once '../vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('1094750969291-k8itgauro8lav6vs58h4i5hb3aji9fn5.apps.googleusercontent.com');
$client->setClientSecret('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');
$redirect_uri = 'https://is35126group25-cinema.onrender.com/auth/google-callback.php';
$client->setRedirectUri($redirect_uri);
$client->setRedirectUri($redirect_uri);

if (isset($_GET['code'])) {
    try {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        $client->setAccessToken($token);

        $google_service = new Google_Service_Oauth2($client);
        $google_user = $google_service->userinfo->get();

        $email = $google_user->email;
        $full_name = $google_user->name;
        $google_id = $google_user->id;

        // Check if user exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['user'] = $user;
        } else {
            // Create new user
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, google_id, role, password_hash) 
                                  VALUES (?, ?, ?, 'customer', '')");
            $stmt->execute([$full_name, $email, $google_id]);
            
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $_SESSION['user'] = $stmt->fetch();
        }

        // Redirect based on role
        if ($_SESSION['user']['role'] == 'admin') {
            header("Location: /admin/dashboard.php");
        } elseif ($_SESSION['user']['role'] == 'staff') {
            header("Location: /staff/dashboard.php");
        } else {
            header("Location: /customer/dashboard.php");
        }
        exit;

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Google Login Failed - No Code Received";
}
?>