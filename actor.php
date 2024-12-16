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
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $show_name = $_POST['show_name'];
    $ratings = $_POST['ratings'];

    $stmt = $conn->prepare("INSERT INTO actors (name, age, gender, show_name, ratings) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sissd", $name, $age, $gender, $show_name, $ratings);
    $stmt->execute();
    $stmt->close();
}

// Handle Read
$result = $conn->query("SELECT * FROM actors");

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $actor_id = $_POST['actor_id'];
    $name = $_POST['name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $show_name = $_POST['show_name'];
    $ratings = $_POST['ratings'];

    $stmt = $conn->prepare("UPDATE actors SET name=?, age=?, gender=?, show_name=?, ratings=? WHERE actor_id=?");
    $stmt->bind_param("sissdi", $name, $age, $gender, $show_name, $ratings, $actor_id);
    $stmt->execute();
    $stmt->close();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $actor_id = $_GET['delete'];
    $conn->query("DELETE FROM actors WHERE actor_id = $actor_id");
    header('Location: actor.php'); // Redirect to avoid re-submission
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Actors</title>
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
    <h1>Manage Actors</h1>

    <h2>Add Actor</h2>
    <form method="POST" action="">
        <input type="hidden" name="actor_id" id="actor_id">
        <input type="text" name="name" placeholder="Name" required class="form-control">
        <input type="number" name="age" placeholder="Age" required class="form-control">
        <select name="gender" required class="form-control">
            <option value="">Select Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>
        <input type="text" name="show_name" placeholder="Show Name" class="form-control">
        <input type="number" step="0.1" name="ratings" placeholder="Ratings (0-10)" class="form-control" min="0" max="10">
        <button type="submit" name="create">Add Actor</button>
    </form>

    <h2>Actor List</h2>
    <table>
        <tr>
            <th>Actor ID</th>
            <th>Name</th>
            <th>Age</th>
            <th>Gender</th>
            <th>Show Name</th>
            <th>Ratings</th>
            <th>Actions</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['actor_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo $row['age']; ?></td>
                    <td><?php echo $row['gender']; ?></td>
                    <td><?php echo htmlspecialchars($row['show_name']); ?></td>
                    <td><?php echo $row['ratings']; ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="actor_id" value="<?php echo $row['actor_id']; ?>">
                            <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                            <input type="number" name="age" value="<?php echo $row['age']; ?>" required>
                            <select name="gender" required>
                                <option value="<?php echo $row['gender']; ?>"><?php echo $row['gender']; ?></option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                            <input type="text" name="show_name" value="<?php echo htmlspecialchars($row['show_name']); ?>">
                            <input type="number" step="0.1" name="ratings" value="<?php echo $row['ratings']; ?>" min="0" max="10">
                            <button type="submit" name="update">Update</button>
                        </form>
                        <a href="?delete=<?php echo $row['actor_id']; ?>">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7">No actors found.</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>
