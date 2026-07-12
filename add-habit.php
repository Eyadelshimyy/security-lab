<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$name = $_POST['habit_name'];
$time = $_POST['habit_time'];

$sql = "INSERT INTO habits (user_id, name, time_of_day) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $user_id, $name, $time);
$stmt->execute();

header("Location: dashboard.php");
exit();
?>
