<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect if not logged in
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Page</title>
    <style>
        body {
            background: linear-gradient(to right, lightpink, lightblue);
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            text-align: center;
        }
        .button {
            display: inline-block; 
            padding: 10px 20px; 
            margin: 10px; 
            text-decoration: none; 
            color: white; 
            background-color: #007BFF; 
            border-radius: 5px; 
        }
        .button:hover { 
            background-color: #0056b3; 
        }
        h1, h2, p {
            margin: 10px;
        }
    </style>
</head>
<body>
    <h1>Welcome to the Drama Theater Management System</h1>
    <p>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>

    <h2>Manage Data</h2>
    <div>
        <a class="button" href="actor.php">Manage Actors</a>
        <a class="button" href="booking.php">Manage Bookings</a>
        <a class="button" href="customer.php">Manage Customers</a>
        <a class="button" href="tickets.php">Manage Tickets</a>
        <a class="button" href="shows.php">Manage Shows</a>
        <a class="button" href="theater.php">Manage Theaters</a>
        <a class="button" href="director.php">Manage Directors</a>
        <a class="button" href="payment.php">Manage Payments</a>
    </div>

    <h2><a href="logout.php" class="button">Logout</a></h2>
</body>
</html>
