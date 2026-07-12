<?php
require "auth.php";
require "csrf.php";
require_login();
csrf_verify();

$user_id = $_SESSION['user_id'];
$goal_id = (int) ($_POST['goal_id'] ?? 0);
$progress = (int) ($_POST['progress'] ?? 0);
$progress = max(0, min(100, $progress));

$stmt = $conn->prepare("UPDATE learning_goals SET progress = ? WHERE id = ? AND user_id = ?");
$stmt->bind_param("iii", $progress, $goal_id, $user_id);
$stmt->execute();

header("Location: learning.php?msg=goal_updated");
exit();
