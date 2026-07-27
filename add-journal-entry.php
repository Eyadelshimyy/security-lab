<?php
require "auth.php";
require "csrf.php";
require_login();
csrf_verify();

$user_id = $_SESSION['user_id'];
$title = trim($_POST['title'] ?? '');
$body = trim($_POST['body'] ?? '');

if ($title === '' || strlen($title) > 150 || $body === '' || strlen($body) > 10000) {
    header("Location: journal.php?msg=entry_error");
    exit();
}

$stmt = $conn->prepare("INSERT INTO journal_entries (user_id, title, body) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $user_id, $title, $body);
$stmt->execute();

header("Location: journal.php?msg=entry_added");
exit();
