<?php
$host = "localhost";
$dbUsername = "webapp_user";
$dbPassword = "password123";
$dbName = "webapp";

$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

if($conn->connect_error) {
	die("Connection faield:". $conn->connect_error);	
}
?>

