<?php
session_start();
require "db.php";

 $username = $_POST['username'];
 $password = $_POST['password'];

 $sql = "select * from users where username = ?";
 $stmt = $conn->prepare($sql);
 $stmt->bind_param("s", $username);
 $stmt->execute();
 $result = $stmt->get_result();

 if ($result->num_rows > 0) {
	$user = $result->fetch_assoc();

	if (password_verify($password , $user['password'])){
		$_SESSION['user_id'] = $user['id'];
		$_SESSION['username'] = $user['username'];
		header("location: dashboard.php");
		exit();	
	} else {
		echo "Invalid password.";
	}
} else {
		echo "No user found with that username.";
	} 
?>
