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
    $price = $_POST['price'];
    $seat_no = $_POST['seat_no'];
    $type = $_POST['type'];
    $show_date_show_time = $_POST['show_date_show_time'];
    $screen_no = $_POST['screen_no'];

    $stmt = $conn->prepare("INSERT INTO tickets (price, seat_no, type, show_date_show_time, screen_no) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("dsssi", $price, $seat_no, $type, $show_date_show_time, $screen_no);
    $stmt->execute();
    $stmt->close();
}

// Handle Read
$result = $conn->query("SELECT * FROM tickets");

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $ticket_serial_no = $_POST['ticket_serial_no'];
    $price = $_POST['price'];
    $seat_no = $_POST['seat_no'];
    $type = $_POST['type'];
    $show_date_show_time = $_POST['show_date_show_time'];
    $screen_no = $_POST['screen_no'];

    $stmt = $conn->prepare("UPDATE tickets SET price=?, seat_no=?, type=?, show_date_show_time=?, screen_no=? WHERE ticket_serial_no=?");
    $stmt->bind_param("dssiii", $price, $seat_no, $type, $show_date_show_time, $screen_no, $ticket_serial_no);
    $stmt->execute();
    $stmt->close();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $ticket_serial_no = $_GET['delete'];
    $conn->query("DELETE FROM tickets WHERE ticket_serial_no = $ticket_serial_no");
    header('Location: tickets.php'); // Redirect to avoid re-submission
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tickets</title>
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
            text-align: center;
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
    <h1>Manage Tickets</h1>

    <h2>Add Ticket</h2>
    <form method="POST" action="">
        <input type="hidden" name="ticket_serial_no" id="ticket_serial_no">
        <input type="number" step="0.01" name="price" placeholder="Price" required class="form-control">
        <input type="text" name="seat_no" placeholder="Seat No" required class="form-control">
        <select name="type" required class="form-control">
            <option value="">Select Ticket Type</option>
            <option value="Standard">Standard</option>
            <option value="VIP">VIP</option>
            <option value="Student">Student</option>
        </select>
        <input type="datetime-local" name="show_date_show_time" required class="form-control">
        <input type="number" name="screen_no" placeholder="Screen No" required class="form-control">
        <button type="submit" name="create">Add Ticket</button>
    </form>

    <h2>Ticket List</h2>
    <table>
        <tr>
            <th>Ticket Serial No</th>
            <th>Price</th>
            <th>Seat No</th>
            <th>Type</th>
            <th>Show Date & Time</th>
            <th>Screen No</th>
            <th>Actions</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['ticket_serial_no']; ?></td>
                    <td><?php echo $row['price']; ?></td>
                    <td><?php echo htmlspecialchars($row['seat_no']); ?></td>
                    <td><?php echo htmlspecialchars($row['type']); ?></td>
                    <td><?php echo $row['show_date_show_time']; ?></td>
                    <td><?php echo $row['screen_no']; ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="ticket_serial_no" value="<?php echo $row['ticket_serial_no']; ?>">
                            <input type="number" step="0.01" name="price" value="<?php echo $row['price']; ?>" required>
                            <input type="text" name="seat_no" value="<?php echo htmlspecialchars($row['seat_no']); ?>" required>
                            <select name="type" required>
                                <option value="<?php echo $row['type']; ?>"><?php echo $row['type']; ?></option>
                                <option value="Standard">Standard</option>
                                <option value="VIP">VIP</option>
                                <option value="Student">Student</option>
                            </select>
                            <input type="datetime-local" name="show_date_show_time" value="<?php echo date('Y-m-d\TH:i', strtotime($row['show_date_show_time'])); ?>" required>
                            <input type="number" name="screen_no" value="<?php echo $row['screen_no']; ?>" required>
                            <button type="submit" name="update">Update</button>
                        </form>
                        <a href="?delete=<?php echo $row['ticket_serial_no']; ?>">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7">No tickets found.</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>
