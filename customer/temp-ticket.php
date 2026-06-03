<?php
require_once '../includes/security.php';
session_start();
require_once '../config/db.php';
require_once '../includes/csrf.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    header("Location: ../auth/login.php");
    exit;
}

$message = '';

if ($_POST) {
    validateCSRF($_POST['csrf_token']);

    $schedule_id = (int)$_POST['schedule_id'];
    $selected_seats = $_POST['selectedSeats'] ?? '';
    $seat_array = array_filter(explode(',', $selected_seats));
    $seats = count($seat_array);

    if ($seats < 1) {
        $message = "❌ Please select at least one seat!";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM schedules WHERE id = ? AND seats_available >= ?");
        $stmt->execute([$schedule_id, $seats]);
        $schedule = $stmt->fetch();

        if ($schedule) {
            $total_amount = $schedule['price'] * $seats;
            $booking_code = "BK" . strtoupper(substr(md5(time()), 0, 8));

            $stmt = $pdo->prepare("INSERT INTO bookings 
                (user_id, schedule_id, seats, selected_seats, total_amount, booking_code) 
                VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_SESSION['user']['id'],
                $schedule_id,
                $seats,
                $selected_seats,
                $total_amount,
                $booking_code
            ]);

            $stmt = $pdo->prepare("UPDATE schedules SET seats_available = seats_available - ? WHERE id = ?");
            $stmt->execute([$seats, $schedule_id]);

            $message = "✅ Booking Successful!<br><strong>Booking Code: $booking_code</strong><br>Seats: " . htmlspecialchars($selected_seats) . "<br>Total: $$total_amount";
        } else {
            $message = "❌ Not enough seats available or invalid schedule!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Book Tickets | CINEMA 25</title>
<style>
body { font-family: Arial; background: #000; color: white; margin: 0; }
.navbar { display: flex; justify-content: space-between; padding: 20px 50px; background: #111; }
.logo { font-size: 24px; font-weight: bold; color: #e50914; }
.nav-links a { color: white; margin-left: 20px; text-decoration: none; }
.booking-page { display: flex; justify-content: center; padding: 40px; }
.booking-card { background: #1b1b1b; padding: 30px; border-radius: 12px; width: 650px; }
.form-group { margin-bottom: 18px; }
label { display: block; margin-bottom: 8px; }
select { width: 100%; padding: 12px; border-radius: 6px; border: none; }
.screen { background: #e50914; text-align: center; padding: 10px; margin: 20px 0; border-radius: 5px; }
.seat-row { margin: 12px 0; }
.seat-row span { display: inline-block; width: 25px; }
.seat { padding: 10px 14px; margin: 5px; background: #444; color: white; border: none; border-radius: 6px; cursor: pointer; }
.seat.selected { background: #ffd700; color: black; }
.seat.booked { background: #777; cursor: not-allowed; }
.booking-summary { background: #111; padding: 20px; margin-top: 20px; border-radius: 8px; }
.auth-btn { width: 100%; padding: 15px; background: #e50914; color: white; border: none; margin-top: 20px; font-size: 16px; cursor: pointer; }
.message { color: #00ff88; font-weight: bold; }
</style>
</head>

<body>

<nav class="navbar">
<div class="logo">CINEMA 25</div>
<div class="nav-links">
<a href="dashboard.php">Dashboard</a>
<a href="my-bookings.php">My Bookings</a>
</div>
</nav>

<div class="booking-page">
<div class="booking-card">

<h2>🎟️ Book Your Movie Ticket</h2>
<p>Welcome, <?= htmlspecialchars($_SESSION['user']['full_name']) ?>!</p>

<?php if ($message): ?>
<p class="message"><?= $message ?></p>
<?php endif; ?>

<form method="POST">
<input type="hidden" name="csrf_token" value="<?= generateCSRF() ?>">
<input type="hidden" id="selectedSeats" name="selectedSeats">

<div class="form-group">
<label>Select Movie Schedule</label>
<select name="schedule_id" id="schedule" required>
<option value="">Choose Movie & Showtime</option>

<?php
$stmt = $pdo->query("SELECT s.*, m.title FROM schedules s 
                    JOIN movies m ON s.movie_id = m.id 
                    WHERE s.seats_available > 0 
                    ORDER BY s.show_date, s.show_time");

while ($row = $stmt->fetch()) {
    echo "<option value='{$row['id']}' data-price='{$row['price']}'>
        " . htmlspecialchars($row['title']) . " - " . htmlspecialchars($row['show_date']) . " " . htmlspecialchars($row['show_time']) . 
        " (" . htmlspecialchars($row['hall']) . ") - $" . htmlspecialchars($row['price']) . "
    </option>";
}
?>

</select>
</div>

<h3>Choose Your Seats</h3>
<div class="screen">SCREEN</div>

<div class="seat-row">
<span>A</span>
<button type="button" class="seat">A1</button>
<button type="button" class="seat">A2</button>
<button type="button" class="seat booked">A3</button>
<button type="button" class="seat">A4</button>
<button type="button" class="seat">A5</button>
</div>

<div class="seat-row">
<span>B</span>
<button type="button" class="seat">B1</button>
<button type="button" class="seat">B2</button>
<button type="button" class="seat">B3</button>
<button type="button" class="seat booked">B4</button>
<button type="button" class="seat">B5</button>
</div>

<div class="seat-row">
<span>C</span>
<button type="button" class="seat">C1</button>
<button type="button" class="seat">C2</button>
<button type="button" class="seat">C3</button>
<button type="button" class="seat">C4</button>
<button type="button" class="seat">C5</button>
</div>

<div class="seat-row">
<span>D</span>
<button type="button" class="seat">D1</button>
<button type="button" class="seat booked">D2</button>
<button type="button" class="seat">D3</button>
<button type="button" class="seat">D4</button>
<button type="button" class="seat">D5</button>
</div>

<div class="booking-summary">
<h3>Booking Summary</h3>
<p><strong>Selected Seats:</strong> <span id="seatOutput">None</span></p>
<p><strong>Tickets:</strong> <span id="ticketCount">0</span></p>
<p><strong>Total:</strong> $<span id="totalPrice">0</span></p>
</div>

<button type="submit" class="auth-btn">Confirm Booking</button>
</form>

</div>
</div>

<script>
const seats = document.querySelectorAll(".seat:not(.booked)");
const seatOutput = document.getElementById("seatOutput");
const ticketCount = document.getElementById("ticketCount");
const totalPrice = document.getElementById("totalPrice");
const selectedSeatsInput = document.getElementById("selectedSeats");
const schedule = document.getElementById("schedule");

function updateSummary() {
    const selectedSeats = document.querySelectorAll(".seat.selected");
    const seatNames = Array.from(selectedSeats).map(seat => seat.textContent);
    const selectedOption = schedule.options[schedule.selectedIndex];
    const price = Number(selectedOption.getAttribute("data-price")) || 0;

    seatOutput.textContent = seatNames.length ? seatNames.join(", ") : "None";
    ticketCount.textContent = seatNames.length;
    totalPrice.textContent = seatNames.length * price;
    selectedSeatsInput.value = seatNames.join(",");
}

seats.forEach(seat => {
    seat.addEventListener("click", () => {
        seat.classList.toggle("selected");
        updateSummary();
    });
});

schedule.addEventListener("change", updateSummary);
</script>

</body>
</html>