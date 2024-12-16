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
    $name = $_POST['name'];
    $phone_no = $_POST['phone_no'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("INSERT INTO customers (name, phone_no, email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $phone_no, $email);
    $stmt->execute();
    $stmt->close();
}

// Handle Read
$result = $conn->query("SELECT * FROM customers");

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $customer_id = $_POST['customer_id'];
    $name = $_POST['name'];
    $phone_no = $_POST['phone_no'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("UPDATE customers SET name=?, phone_no=?, email=? WHERE customer_id=?");
    $stmt->bind_param("sssi", $name, $phone_no, $email, $customer_id);
    $stmt->execute();
    $stmt->close();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $customer_id = $_GET['delete'];
    $conn->query("DELETE FROM customers WHERE customer_id = $customer_id");
    header('Location: customer.php'); // Redirect to avoid re-submission
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Customers</title>
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
    <h1>Manage Customers</h1>

    <h2>Add Customer</h2>
    <form method="POST" action="">
        <input type="hidden" name="customer_id" id="customer_id">
        <input type="text" name="name" placeholder="Name" required class="form-control">
        <input type="text" name="phone_no" placeholder="Phone No" required class="form-control">
        <input type="email" name="email" placeholder="Email" required class="form-control">
        <button type="submit" name="create">Add Customer</button>
    </form>

    <h2>Customer List</h2>
    <table>
        <tr>
            <th>Customer ID</th>
            <th>Name</th>
            <th>Phone No</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['customer_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone_no']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="customer_id" value="<?php echo $row['customer_id']; ?>">
                            <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                            <input type="text" name="phone_no" value="<?php echo htmlspecialchars($row['phone_no']); ?>" required>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
                            <button type="submit" name="update">Update</button>
                        </form>
                        <a href="?delete=<?php echo $row['customer_id']; ?>">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5">No customers found.</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>
