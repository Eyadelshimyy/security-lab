<?php
$host = "localhost";
$dbUsername = "webapp_user";
$dbPassword = "password123";
$dbName = "webapp";

$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

if($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>

