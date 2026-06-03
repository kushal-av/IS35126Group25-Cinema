<?php
// staff/dashboard.php
require_once '../includes/security.php';
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'staff') {
    header("Location: ../auth/login.php");
    exit;
}

$user = $_SESSION['user'];

$total_bookings = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$confirmed_bookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'confirmed'")->fetchColumn();
$pending_bookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Staff Dashboard - CINEMA 25</title>

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

.film-grain {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: repeating-linear-gradient(
        45deg,
        rgba(255,255,255,0.04),
        rgba(255,255,255,0.04) 2px,
        transparent 2px,
        transparent 8px
    );
    z-index: -1;
}

.container {
    position: relative;
    z-index: 2;
    max-width: 1100px;
    margin: 0 auto;
    padding: 80px 20px;
}

h1 {
    text-align: center;
    font-size: 3.5rem;
    margin-bottom: 20px;
    text-shadow: 0 0 30px #e50914;
}

.welcome {
    text-align: center;
    font-size: 1.6rem;
    margin-bottom: 40px;
    opacity: 0.9;
}

.stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 20px;
    margin-bottom: 50px;
}

.stat-box {
    background: rgba(30,0,0,0.8);
    padding: 25px;
    border-radius: 16px;
    border: 1px solid #e50914;
    text-align: center;
    box-shadow: 0 0 25px rgba(229,9,20,0.3);
}

.stat-box h2 {
    color: #ffd700;
    font-size: 2rem;
    margin-bottom: 10px;
}

.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 25px;
}

.card {
    background: rgba(30,0,0,0.8);
    padding: 30px;
    border-radius: 16px;
    border: 1px solid #e50914;
    text-align: center;
    transition: all 0.4s;
}

.card:hover {
    transform: translateY(-10px);
    box-shadow: 0 0 40px rgba(229,9,20,0.5);
}

.card h2 {
    margin-bottom: 15px;
}

.card p {
    margin-bottom: 20px;
    opacity: 0.9;
}

.btn {
    display: block;
    padding: 16px;
    margin: 15px 0;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
    font-size: 1.1rem;
}

.btn-validate { background: #e50914; color: white; }
.btn-bookings { background: #ffd700; color: #000; }
.btn-logout { background: #444; color: white; }
</style>
</head>

<body>
<div class="bg"></div>
<div class="film-grain"></div>

<div class="container">
    <h1>🎫 Staff Dashboard</h1>
    <p class="welcome">Hello, <strong><?= htmlspecialchars($user['full_name']) ?></strong>! You are logged in as Staff.</p>

    <div class="stats">
        <div class="stat-box">
            <h2><?= $total_bookings ?></h2>
            <p>Total Bookings</p>
        </div>

        <div class="stat-box">
            <h2><?= $confirmed_bookings ?></h2>
            <p>Confirmed Tickets</p>
        </div>

        <div class="stat-box">
            <h2><?= $pending_bookings ?></h2>
            <p>Pending Tickets</p>
        </div>
    </div>

    <div class="cards">
        <div class="card">
            <h2>✅ Validate Tickets</h2>
            <p>Check customer booking codes and confirm ticket validity.</p>
            <a href="validate-ticket.php" class="btn btn-validate">Validate Ticket</a>
        </div>

        <div class="card">
            <h2>📋 View Bookings</h2>
            <p>View customer bookings for ticket verification.</p>
            <a href="bookings.php" class="btn btn-bookings">View Bookings</a>
        </div>

        <div class="card">
            <h2>🚪 Logout</h2>
            <p>End your staff session securely.</p>
            <a href="../auth/logout.php" class="btn btn-logout">Logout</a>
        </div>
    </div>
</div>

</body>
</html>