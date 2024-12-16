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
    $customer_id = $_POST['customer_id'];
    $booking_id = $_POST['booking_id'];
    $payment_date = $_POST['payment_date'];
    $payment_method = $_POST['payment_method'];
    $amount = $_POST['amount'];

    $stmt = $conn->prepare("INSERT INTO payment (customer_id, booking_id, payment_date, payment_method, amount) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iissi", $customer_id, $booking_id, $payment_date, $payment_method, $amount);
    $stmt->execute();
    $stmt->close();
}

// Handle Read
$result = $conn->query("SELECT * FROM payment");

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $payment_id = $_POST['payment_id'];
    $customer_id = $_POST['customer_id'];
    $booking_id = $_POST['booking_id'];
    $payment_date = $_POST['payment_date'];
    $payment_method = $_POST['payment_method'];
    $amount = $_POST['amount'];

    $stmt = $conn->prepare("UPDATE payment SET customer_id=?, booking_id=?, payment_date=?, payment_method=?, amount=? WHERE payment_id=?");
    $stmt->bind_param("iissii", $customer_id, $booking_id, $payment_date, $payment_method, $amount, $payment_id);
    $stmt->execute();
    $stmt->close();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $payment_id = $_GET['delete'];
    $conn->query("DELETE FROM payment WHERE payment_id = $payment_id");
    header('Location: payment.php'); // Redirect to avoid re-submission
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Payments</title>
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
    <h1>Manage Payments</h1>

    <h2>Add Payment</h2>
    <form method="POST" action="">
        <input type="hidden" name="payment_id" id="payment_id">
        <input type="number" name="customer_id" placeholder="Customer ID" required class="form-control">
        <input type="number" name="booking_id" placeholder="Booking ID" required class="form-control">
        <input type="datetime-local" name="payment_date" required class="form-control">
        <input type="text" name="payment_method" placeholder="Payment Method" required class="form-control">
        <input type="number" step="0.01" name="amount" placeholder="Amount" required class="form-control">
        <button type="submit" name="create">Add Payment</button>
    </form>

    <h2>Payment List</h2>
    <table>
        <tr>
            <th>Payment ID</th>
            <th>Customer ID</th>
            <th>Booking ID</th>
            <th>Payment Date</th>
            <th>Payment Method</th>
            <th>Amount</th>
            <th>Actions</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['payment_id']; ?></td>
                    <td><?php echo $row['customer_id']; ?></td>
                    <td><?php echo $row['booking_id']; ?></td>
                    <td><?php echo $row['payment_date']; ?></td>
                    <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                    <td><?php echo $row['amount']; ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="payment_id" value="<?php echo $row['payment_id']; ?>">
                            <input type="number" name="customer_id" value="<?php echo $row['customer_id']; ?>" required>
                            <input type="number" name="booking_id" value="<?php echo $row['booking_id']; ?>" required>
                            <input type="datetime-local" name="payment_date" value="<?php echo date('Y-m-d\TH:i', strtotime($row['payment_date'])); ?>" required>
                            <input type="text" name="payment_method" value="<?php echo htmlspecialchars($row['payment_method']); ?>" required>
                            <input type="number" step="0.01" name="amount" value="<?php echo $row['amount']; ?>" required>
                            <button type="submit" name="update">Update</button>
                        </form>
                        <a href="?delete=<?php echo $row['payment_id']; ?>">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7">No payments found.</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>
