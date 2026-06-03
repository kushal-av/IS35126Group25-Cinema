<?php
session_start();
require_once '../config/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Now Showing - CINEMA 25</title>
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
            background: linear-gradient(rgba(10,0,0,0.85), rgba(0,0,0,0.9)),
                        url('https://picsum.photos/id/1015/1920/1080') center/cover no-repeat;
            z-index: -2;
        }
        .film-grain {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: repeating-linear-gradient(45deg, rgba(255,255,255,0.04), rgba(255,255,255,0.04) 2px, transparent 2px, transparent 8px);
            animation: grain 6s steps(8) infinite;
            z-index: -1;
        }
        @keyframes grain { 0% { transform: translate(0,0); } 10% { transform: translate(-1%,2%); } 20% { transform: translate(2%,-2%); } 100% { transform: translate(0,0); } }

        .container { 
            position: relative; 
            z-index: 2; 
            max-width: 1200px; 
            margin: 0 auto; 
            padding: 40px 20px; 
        }
        h1 { 
            text-align: center; 
            font-size: 3.8rem; 
            margin-bottom: 50px; 
            text-shadow: 0 0 40px #e50914; 
        }
        .movie-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }
        .movie-card {
            background: rgba(30,0,0,0.85);
            border-radius: 12px;
            padding: 20px;
            transition: all 0.4s;
        }
        .movie-card:hover {
            transform: scale(1.05);
            box-shadow: 0 0 30px rgba(229,9,20,0.6);
        }
        .movie-info h3 {
            margin-bottom: 10px;
            color: #ffd700;
        }
        .btn {
            display: block;
            padding: 14px;
            background: #e50914;
            color: white;
            text-decoration: none;
            text-align: center;
            border-radius: 8px;
            margin-top: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="bg"></div>
    <div class="film-grain"></div>

    <div class="container">
        <h1>NOW SHOWING</h1>
        
        <div class="movie-grid">
            <?php
            $stmt = $pdo->query("SELECT * FROM movies ORDER BY status, title");
            while ($movie = $stmt->fetch()) {
                echo "
                <div class='movie-card'>
                    <div class='movie-info'>
                        <h3>" . htmlspecialchars($movie['title']) . "</h3>
                        <p>" . htmlspecialchars($movie['genre']) . " • " . $movie['duration'] . " min</p>
                        <p>" . htmlspecialchars($movie['description']) . "</p>
                        <a href='/customer/book-ticket.php?movie_id=" . $movie['id'] . "' class='btn'>Book Ticket</a>
                    </div>
                </div>";
            }
            ?>
        </div>
    </div>
</body>
</html>