<?php
require "db.php";

$username = $_POST['username'];
$password = $_POST['password'];
$email = $_POST['email'];

//password hashing
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (username,password,email) VALUES(?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss",$username,$hashedPassword, $email);

if($stmt->execute()) {
	echo "Registration successful! <a href='login.html'>login here</a>";
} else {
	echo "ERROR: " . $stmt->error;
}
?>
