<?php
require "auth.php";
require "csrf.php";
require_login();
csrf_verify();

$user_id = $_SESSION['user_id'];
$name = trim($_POST['habit_name'] ?? '');
$time = trim($_POST['habit_time'] ?? '');

if ($name === '' || strlen($name) > 100) {
    header("Location: habits.php?msg=habit_error");
    exit();
}

$sql = "INSERT INTO habits (user_id, name, time_of_day) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $user_id, $name, $time);
$stmt->execute();

header("Location: habits.php?msg=habit_added");
exit();
