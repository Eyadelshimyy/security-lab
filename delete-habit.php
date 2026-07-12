<?php
require "auth.php";
require "csrf.php";
require_login();
csrf_verify();

$user_id = $_SESSION['user_id'];
$habit_id = (int) ($_POST['habit_id'] ?? 0);

$stmt = $conn->prepare("DELETE FROM habits WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $habit_id, $user_id);
$stmt->execute();

header("Location: habits.php?msg=habit_deleted");
exit();
