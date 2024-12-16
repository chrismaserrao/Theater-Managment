<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect if not logged in
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'my_theater_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Create
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    $date = $_POST['date'];
    $show_id = $_POST['show_id'];
    $seats_no = $_POST['seats_no'];
    $customer_id = $_POST['customer_id'];

    $stmt = $conn->prepare("INSERT INTO bookings (date, show_id, seats_no, customer_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siiii", $date, $show_id, $seats_no, $customer_id);
    $stmt->execute();
    $stmt->close();
}

// Handle Read
$result = $conn->query("SELECT * FROM bookings");

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $booking_id = $_POST['booking_id'];
    $date = $_POST['date'];
    $show_id = $_POST['show_id'];
    $seats_no = $_POST['seats_no'];
    $customer_id = $_POST['customer_id'];

    $stmt = $conn->prepare("UPDATE bookings SET date=?, show_id=?, seats_no=?, customer_id=? WHERE booking_id=?");
    $stmt->bind_param("siiii", $date, $show_id, $seats_no, $customer_id, $booking_id);
    $stmt->execute();
    $stmt->close();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $booking_id = $_GET['delete'];
    $conn->query("DELETE FROM bookings WHERE booking_id = $booking_id");
    header('Location: booking.php'); // Redirect to avoid re-submission
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
        .form-control { margin-bottom: 10px; }
    </style>
    <style>
        body {
            background: linear-gradient(to right, lightpink, lightyellow);
            font-family: Arial, sans-serif;
            padding: 20px;
            margin: 0;
            
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        .form-control {
            margin-bottom: 10px;
            display: block;
            width: calc(100% - 22px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin: 5px auto;
            max-width: 400px;
        }
        button {
            padding: 10px 15px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Manage Bookings</h1>

    <h2>Add Booking</h2>
    <form method="POST" action="">
        <input type="hidden" name="booking_id" id="booking_id">
        <input type="date" name="date" required class="form-control">
        <input type="number" name="show_id" placeholder="Show ID" required class="form-control">
        <input type="number" name="seats_no" placeholder="Seats No" required class="form-control">
        <input type="number" name="customer_id" placeholder="Customer ID" required class="form-control">
        <button type="submit" name="create">Add Booking</button>
    </form>

    <h2>Booking List</h2>
    <table>
        <tr>
            <th>Booking ID</th>
            <th>Date</th>
            <th>Show ID</th>
            <th>Seats No</th>
            <th>Customer ID</th>
            <th>Actions</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['booking_id']; ?></td>
                    <td><?php echo $row['date']; ?></td>
                    <td><?php echo $row['show_id']; ?></td>
                    <td><?php echo $row['seats_no']; ?></td>
                    <td><?php echo $row['customer_id']; ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                            <input type="date" name="date" value="<?php echo $row['date']; ?>" required>
                            <input type="number" name="show_id" value="<?php echo $row['show_id']; ?>" required>
                            <input type="number" name="seats_no" value="<?php echo $row['seats_no']; ?>" required>
                            <input type="number" name="customer_id" value="<?php echo $row['customer_id']; ?>" required>
                            <button type="submit" name="update">Update</button>
                        </form>
                        <a href="?delete=<?php echo $row['booking_id']; ?>">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6">No bookings found.</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>
