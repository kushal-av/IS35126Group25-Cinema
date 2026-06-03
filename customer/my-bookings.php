<?php
// customer/my-bookings.php
require_once '../includes/security.php';
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    header("Location: ../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Group 25</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background: #e50914; color: white; }
    </style>
</head>
<body>
    <h2>🎟️ My Bookings</h2>
    <p>Welcome, <?= htmlspecialchars($_SESSION['user']['full_name']) ?>!</p>
    <a href="dashboard.php">← Back to Dashboard</a><br><br>

    <?php
    $stmt = $pdo->prepare("SELECT b.*, m.title, s.show_date, s.show_time, s.hall 
                          FROM bookings b 
                          JOIN schedules s ON b.schedule_id = s.id 
                          JOIN movies m ON s.movie_id = m.id 
                          WHERE b.user_id = ? 
                          ORDER BY b.booked_at DESC");
    $stmt->execute([$_SESSION['user']['id']]);
    $bookings = $stmt->fetchAll();
    ?>

    <?php if (count($bookings) > 0): ?>
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
            <?php foreach ($bookings as $b): ?>
            <tr>
                <td><strong><?= htmlspecialchars($b['booking_code']) ?></strong></td>
                <td><?= htmlspecialchars($b['title']) ?></td>
                <td><?= $b['show_date'] ?> <?= $b['show_time'] ?></td>
                <td><?= $b['hall'] ?></td>
                <td><?= $b['seats'] ?></td>
                <td>$<?= number_format($b['total_amount'], 2) ?></td>
                <td><?= $b['status'] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>You have no bookings yet.</p>
    <?php endif; ?>
</body>
</html>