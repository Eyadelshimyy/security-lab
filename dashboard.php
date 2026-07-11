<?php
session_start();

if (!isset($_SESSION['user_id'])) {
	header("location: login.html");
	exit();
	
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Dashboard</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
	<h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
	<p>This is your protected dashboard.</p>
	<a href="logout.php">Logout</a>
</body>
</html>
