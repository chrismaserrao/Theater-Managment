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
    $experience_years = $_POST['experience_years'];
    $shows_name = $_POST['shows_name'];

    $stmt = $conn->prepare("INSERT INTO director (name, experience_years, shows_name) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $name, $experience_years, $shows_name);
    $stmt->execute();
    $stmt->close();
}

// Handle Read
$result = $conn->query("SELECT * FROM director");

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $director_id = $_POST['director_id'];
    $name = $_POST['name'];
    $experience_years = $_POST['experience_years'];
    $shows_name = $_POST['shows_name'];

    $stmt = $conn->prepare("UPDATE director SET name=?, experience_years=?, shows_name=? WHERE director_id=?");
    $stmt->bind_param("sisi", $name, $experience_years, $shows_name, $director_id);
    $stmt->execute();
    $stmt->close();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $director_id = $_GET['delete'];
    $conn->query("DELETE FROM director WHERE director_id = $director_id");
    header('Location: director.php'); // Redirect to avoid re-submission
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Directors</title>
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
    <h1>Manage Directors</h1>

    <h2>Add Director</h2>
    <form method="POST" action="">
        <input type="hidden" name="director_id" id="director_id">
        <input type="text" name="name" placeholder="Director Name" required class="form-control">
        <input type="number" name="experience_years" placeholder="Experience Years" required class="form-control">
        <input type="text" name="shows_name" placeholder="Shows Name" required class="form-control">
        <button type="submit" name="create">Add Director</button>
    </form>

    <h2>Director List</h2>
    <table>
        <tr>
            <th>Director ID</th>
            <th>Name</th>
            <th>Experience Years</th>
            <th>Shows Name</th>
            <th>Actions</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['director_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo $row['experience_years']; ?></td>
                    <td><?php echo htmlspecialchars($row['shows_name']); ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="director_id" value="<?php echo $row['director_id']; ?>">
                            <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                            <input type="number" name="experience_years" value="<?php echo $row['experience_years']; ?>" required>
                            <input type="text" name="shows_name" value="<?php echo htmlspecialchars($row['shows_name']); ?>" required>
                            <button type="submit" name="update">Update</button>
                        </form>
                        <a href="?delete=<?php echo $row['director_id']; ?>">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5">No directors found.</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>
