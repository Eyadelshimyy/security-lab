<?php
require "auth.php";
require "csrf.php";
require_login();
csrf_verify();

$user_id = $_SESSION['user_id'];
$title = trim($_POST['title'] ?? '');

if ($title === '' || strlen($title) > 150) {
    header("Location: learning.php?msg=goal_error");
    exit();
}

$stmt = $conn->prepare("INSERT INTO learning_goals (user_id, title, progress) VALUES (?, ?, 0)");
$stmt->bind_param("is", $user_id, $title);
$stmt->execute();

header("Location: learning.php?msg=goal_added");
exit();
