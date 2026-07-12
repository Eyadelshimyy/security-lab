<?php
require "auth.php";
require "csrf.php";
require_login();
csrf_verify();

$user_id = $_SESSION['user_id'];
$goal_id = (int) ($_POST['goal_id'] ?? 0);

$stmt = $conn->prepare("DELETE FROM learning_goals WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $goal_id, $user_id);
$stmt->execute();

header("Location: learning.php?msg=goal_deleted");
exit();
