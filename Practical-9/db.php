<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "database 9";

$conn = new mysqli(
    $servername,
    $username,
    $password,
    $database
);

if ($conn->connect_error) {
    die("Database connection failed.");
}
?>
