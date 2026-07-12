<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$habit_id = $_POST['habit_id'];
$today = date("Y-m-d");

// Check if a log already exists for today
$check = $conn->prepare("SELECT id FROM habit_logs WHERE habit_id = ? AND log_date = ?");
$check->bind_param("is", $habit_id, $today);
$check->execute();
$existing = $check->get_result();

if ($existing->num_rows > 0) {
    // Already logged today - toggle it off
    $stmt = $conn->prepare("DELETE FROM habit_logs WHERE habit_id = ? AND log_date = ?");
    $stmt->bind_param("is", $habit_id, $today);
    $stmt->execute();
} else {
    // Not logged yet - mark as done
    $stmt = $conn->prepare("INSERT INTO habit_logs (habit_id, log_date, completed) VALUES (?, ?, 1)");
    $stmt->bind_param("is", $habit_id, $today);
    $stmt->execute();
}

header("Location: dashboard.php");
exit();
?>
