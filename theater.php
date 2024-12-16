<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'my_theater_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Create
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $capacity = $_POST['capacity'];

    $stmt = $conn->prepare("INSERT INTO theater (name, location, capacity) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $name, $location, $capacity);
    $stmt->execute();
    $stmt->close();
}

// Handle Read
$result = $conn->query("SELECT * FROM theater");

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $theater_id = $_POST['theater_id'];
    $name = $_POST['name'];
    $location = $_POST['location'];
    $capacity = $_POST['capacity'];

    $stmt = $conn->prepare("UPDATE theater SET name=?, location=?, capacity=? WHERE theater_id=?");
    $stmt->bind_param("ssii", $name, $location, $capacity, $theater_id);
    $stmt->execute();
    $stmt->close();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $theater_id = $_GET['delete'];
    $conn->query("DELETE FROM theater WHERE theater_id = $theater_id");
    header('Location: theater.php');
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Theater</title>
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
    <h1>Manage Theater</h1>

    <h2>Add Theater</h2>
    <form method="POST" action="">
        <input type="hidden" name="theater_id" id="theater_id">
        <input type="text" name="name" placeholder="Theater Name" required class="form-control">
        <input type="text" name="location" placeholder="Location" required class="form-control">
        <input type="number" name="capacity" placeholder="Capacity" required class="form-control">
        <button type="submit" name="create">Add Theater</button>
    </form>

    <h2>Theater List</h2>
    <table>
        <tr>
            <th>Theater ID</th>
            <th>Name</th>
            <th>Location</th>
            <th>Capacity</th>
            <th>Actions</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['theater_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                    <td><?php echo $row['capacity']; ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="theater_id" value="<?php echo $row['theater_id']; ?>">
                            <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                            <input type="text" name="location" value="<?php echo htmlspecialchars($row['location']); ?>" required>
                            <input type="number" name="capacity" value="<?php echo $row['capacity']; ?>" required>
                            <button type="submit" name="update">Update</button>
                        </form>
                        <a href="?delete=<?php echo $row['theater_id']; ?>">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5">No theaters found.</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>
