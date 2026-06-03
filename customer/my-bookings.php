<?php
// customer/my-bookings.php
require_once '../includes/security.php';
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    header("Location: ../auth/login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT b.*, m.title, s.show_date, s.show_time, s.hall 
                      FROM bookings b 
                      JOIN schedules s ON b.schedule_id = s.id 
                      JOIN movies m ON s.movie_id = m.id 
                      WHERE b.user_id = ? 
                      ORDER BY b.booked_at DESC");
$stmt->execute([$_SESSION['user']['id']]);
$bookings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Bookings | CINEMA 25</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:'Helvetica Neue',Arial,sans-serif;
    min-height:100vh;
    background:
        linear-gradient(rgba(10,0,0,0.85), rgba(0,0,0,0.9)),
        url('https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=1600') center/cover;
    color:white;
    padding:40px;
}

.container{
    max-width:1200px;
    margin:auto;
}

h2{
    text-align:center;
    color:#e50914;
    font-size:3rem;
    margin-bottom:15px;
}

.welcome{
    text-align:center;
    font-size:1.2rem;
    margin-bottom:30px;
}

.back-btn{
    display:inline-block;
    padding:12px 25px;
    background:#e50914;
    color:white;
    text-decoration:none;
    border-radius:30px;
    font-weight:bold;
    margin-bottom:25px;
    transition:0.3s;
}

.back-btn:hover{
    background:#ff1f2f;
}

table{
    width:100%;
    border-collapse:collapse;
    background:rgba(255,255,255,0.08);
    backdrop-filter:blur(10px);
    border-radius:15px;
    overflow:hidden;
}

th{
    background:#e50914;
    color:white;
    padding:15px;
}

td{
    padding:15px;
    border-bottom:1px solid rgba(255,255,255,0.1);
}

tr:hover{
    background:rgba(255,255,255,0.05);
}

.no-bookings{
    text-align:center;
    margin-top:50px;
    font-size:1.3rem;
}

.status{
    color:#00ff88;
    font-weight:bold;
}
</style>
</head>

<body>

<div class="container">

    <h2>🎟️ My Bookings</h2>

    <p class="welcome">
        Welcome, <strong><?= htmlspecialchars($_SESSION['user']['full_name']) ?></strong>!
    </p>

    <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>

    <?php if(count($bookings) > 0): ?>

    <table>
        <tr>
            <th>Booking Code</th>
            <th>Movie</th>
            <th>Date & Time</th>
            <th>Hall</th>
            <th>Seats</th>
            <th>Total Amount</th>
            <th>Status</th>
        </tr>

        <?php foreach($bookings as $b): ?>
        <tr>
            <td><strong><?= htmlspecialchars($b['booking_code']) ?></strong></td>
            <td><?= htmlspecialchars($b['title']) ?></td>
            <td><?= htmlspecialchars($b['show_date']) ?> <?= htmlspecialchars($b['show_time']) ?></td>
            <td><?= htmlspecialchars($b['hall']) ?></td>
            <td><?= htmlspecialchars($b['seats']) ?></td>
            <td>$<?= number_format($b['total_amount'], 2) ?></td>
            <td class="status"><?= htmlspecialchars($b['status']) ?></td>
        </tr>
        <?php endforeach; ?>

    </table>

    <?php else: ?>

    <p class="no-bookings">🎬 You have no bookings yet.</p>

    <?php endif; ?>

</div>

</body>
</html>