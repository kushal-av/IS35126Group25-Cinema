<?php
// auth/google-login.php

require_once '../includes/security.php';
session_start();
require_once '../vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('1094750969291-k8itgauro8lav6vs58h4i5hb3aji9fn5.apps.googleusercontent.com');
$client->setClientSecret('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');
$redirect_uri = 'https://is35126group25-cinema.onrender.com/auth/google-callback.php';
$client->setRedirectUri($redirect_uri);
$client->setRedirectUri($redirect_uri);
$client->addScope('email');
$client->addScope('profile');

header('Location: ' . $client->createAuthUrl());
exit;
?>