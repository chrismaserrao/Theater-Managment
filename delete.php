<?php
if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "my_theater_db";

    // Create connection
    $connection = new mysqli($servername, $username, $password, $database);

    // Delete record
    $sql = "DELETE FROM customer WHERE id=$id";
    $connection->query($sql);

    // Redirect to index page
    header("location: /my_theater_db/index.php");
    exit;
}
?>
