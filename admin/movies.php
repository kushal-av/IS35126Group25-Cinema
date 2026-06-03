<?php
// public/movies.php
require_once '../includes/security.php';
session_start();
require_once '../config/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movies - Cinema Group 25</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .movie-card {
            border: 1px solid #ddd;
            padding: 15px;
            margin: 15px;
            width: 300px;
            display: inline-block;
            vertical-align: top;
            border-radius: 8px;
        }
        .btn { padding: 10px 15px; background: #e50914; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>🎥 Now Showing & Upcoming Movies</h1>
    <a href="../public/index.php">← Back to Home</a><br><br>

    <?php
    $stmt = $pdo->query("SELECT * FROM movies ORDER BY status, title");
    $movies = $stmt->fetchAll();

    if (count($movies) > 0):
        foreach ($movies as $movie):
    ?>
        <div class="movie-card">
            <h3><?= htmlspecialchars($movie['title']) ?></h3>
            <p><strong>Genre:</strong> <?= htmlspecialchars($movie['genre']) ?></p>
            <p><strong>Duration:</strong> <?= $movie['duration'] ?> minutes</p>
            <p><?= htmlspecialchars($movie['description']) ?></p>
            <p><strong>Status:</strong> <?= str_replace('_', ' ', $movie['status']) ?></p>
            
            <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 'customer'): ?>
                <a href="../customer/book-ticket.php?movie_id=<?= $movie['id'] ?>" class="btn">Book Ticket</a>
            <?php endif; ?>
        </div>
    <?php
        endforeach;
    else:
        echo "<p>No movies added yet.</p>";
    endif;
    ?>
</body>
</html>