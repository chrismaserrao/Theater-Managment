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
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $show_name = $_POST['show_name'];
    $date = $_POST['date'];
    $show_no = $_POST['show_no'];

    $stmt = $conn->prepare("INSERT INTO shows (start_time, end_time, show_name, date, show_no) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $start_time, $end_time, $show_name, $date, $show_no);
    $stmt->execute();
    $stmt->close();
}

// Handle Read
$result = $conn->query("SELECT * FROM shows");

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $show_id = $_POST['show_id'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $show_name = $_POST['show_name'];
    $date = $_POST['date'];
    $show_no = $_POST['show_no'];

    $stmt = $conn->prepare("UPDATE shows SET start_time=?, end_time=?, show_name=?, date=?, show_no=? WHERE show_id=?");
    $stmt->bind_param("ssssii", $start_time, $end_time, $show_name, $date, $show_no, $show_id);
    $stmt->execute();
    $stmt->close();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $show_id = $_GET['delete'];
    $conn->query("DELETE FROM shows WHERE show_id = $show_id");
    header('Location: show.php'); // Redirect to avoid re-submission
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Shows</title>
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
    <h1>Manage Shows</h1>

    <h2>Add Show</h2>
    <form method="POST" action="">
        <input type="hidden" name="show_id" id="show_id">
        <input type="datetime-local" name="start_time" required class="form-control">
        <input type="datetime-local" name="end_time" required class="form-control">
        <input type="text" name="show_name" placeholder="Show Name" required class="form-control">
        <input type="date" name="date" required class="form-control">
        <input type="number" name="show_no" placeholder="Show No" required class="form-control">
        <button type="submit" name="create">Add Show</button>
    </form>

    <h2>Show List</h2>
    <table>
        <tr>
            <th>Show ID</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Show Name</th>
            <th>Date</th>
            <th>Show No</th>
            <th>Actions</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['show_id']; ?></td>
                    <td><?php echo $row['start_time']; ?></td>
                    <td><?php echo $row['end_time']; ?></td>
                    <td><?php echo htmlspecialchars($row['show_name']); ?></td>
                    <td><?php echo $row['date']; ?></td>
                    <td><?php echo $row['show_no']; ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="show_id" value="<?php echo $row['show_id']; ?>">
                            <input type="datetime-local" name="start_time" value="<?php echo date('Y-m-d\TH:i', strtotime($row['start_time'])); ?>" required>
                            <input type="datetime-local" name="end_time" value="<?php echo date('Y-m-d\TH:i', strtotime($row['end_time'])); ?>" required>
                            <input type="text" name="show_name" value="<?php echo htmlspecialchars($row['show_name']); ?>" required>
                            <input type="date" name="date" value="<?php echo $row['date']; ?>" required>
                            <input type="number" name="show_no" value="<?php echo $row['show_no']; ?>" required>
                            <button type="submit" name="update">Update</button>
                        </form>
                        <a href="?delete=<?php echo $row['show_id']; ?>">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7">No shows found.</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>
