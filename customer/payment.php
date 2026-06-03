<?php
require_once '../includes/security.php';
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    header("Location: ../auth/login.php");
    exit;
}

$message = '';

$stmt = $pdo->prepare("SELECT b.*, m.title, s.show_date, s.show_time, s.hall 
                      FROM bookings b
                      JOIN schedules s ON b.schedule_id = s.id
                      JOIN movies m ON s.movie_id = m.id
                      WHERE b.user_id = ?
                      ORDER BY b.booked_at DESC
                      LIMIT 1");
$stmt->execute([$_SESSION['user']['id']]);
$booking = $stmt->fetch();

if ($_POST && $booking) {
    $payment_method = htmlspecialchars($_POST['payment_method']);
    $message = "✅ Payment Successful using $payment_method!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payment Dashboard - CINEMA 25</title>
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
    max-width: 500px;
    margin: 80px auto;
    padding: 40px 30px;
    background: rgba(20,0,0,0.85);
    border-radius: 16px;
    border: 1px solid #e50914;
    box-shadow: 0 0 40px rgba(229,9,20,0.4);
}

h2 {
    text-align: center;
    margin-bottom: 25px;
    font-size: 2.2rem;
}

.summary {
    background: #111;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 25px;
    line-height: 1.8;
}

select, input {
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

.success {
    color: #00ff88;
    text-align: center;
    font-weight: bold;
    margin-bottom: 15px;
}
.btn-payment {
    background: #28a745;
    color: white;
}

.btn-payment:hover {
    background: #218838;
}
a {
    color: #ffd700;
    text-decoration: none;
}
</style>
</head>

<body>
<div class="bg"></div>

<div class="container">
<h2>💳 Payment Dashboard</h2>

<?php if ($message): ?>
<p class="success"><?= $message ?></p>
<?php endif; ?>

<?php if ($booking): ?>

<div class="summary">
<p><strong>Booking Code:</strong> <?= htmlspecialchars($booking['booking_code']) ?></p>
<p><strong>Movie:</strong> <?= htmlspecialchars($booking['title']) ?></p>
<p><strong>Date & Time:</strong> <?= htmlspecialchars($booking['show_date']) ?> <?= htmlspecialchars($booking['show_time']) ?></p>
<p><strong>Hall:</strong> <?= htmlspecialchars($booking['hall']) ?></p>
<p><strong>Seats:</strong> <?= htmlspecialchars($booking['selected_seats'] ?? $booking['seats']) ?></p>
<p><strong>Total Amount:</strong> $<?= number_format($booking['total_amount'], 2) ?></p>
</div>

<form method="POST">
<label>Select Payment Method</label>
<select name="payment_method" required>
<option value="">Choose Payment Method</option>
<option value="Credit Card">Credit Card</option>
<option value="Debit Card">Debit Card</option>
<option value="MPaisa">MPaisa</option>
<option value="Cash at Counter">Cash at Counter</option>
</select>

<input type="text" placeholder="Card / Transaction Reference Number" required>

<button type="submit">Confirm Payment</button>
</form>

<?php else: ?>
<p>You have no booking to pay for.</p>
<?php endif; ?>

<br>
<p style="text-align:center;">
<a href="dashboard.php">← Back to Dashboard</a>
</p>

</div>
</body>
</html>