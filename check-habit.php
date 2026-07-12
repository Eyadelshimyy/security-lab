<?php
require "auth.php";
require "csrf.php";
require_login();
csrf_verify();

$user_id = $_SESSION['user_id'];
$habit_id = (int) ($_POST['habit_id'] ?? 0);
$today = date("Y-m-d");

// Make sure the habit actually belongs to the logged-in user before touching it.
$owns = $conn->prepare("SELECT id FROM habits WHERE id = ? AND user_id = ?");
$owns->bind_param("ii", $habit_id, $user_id);
$owns->execute();
if ($owns->get_result()->num_rows === 0) {
    header("Location: habits.php");
    exit();
}

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

header("Location: habits.php");
exit();
