<?php
require "auth.php";
require "csrf.php";
require_login();
csrf_verify();

$user_id = $_SESSION['user_id'];
$entry_id = (int) ($_POST['entry_id'] ?? 0);

$stmt = $conn->prepare("DELETE FROM journal_entries WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $entry_id, $user_id);
$stmt->execute();

header("Location: journal.php?msg=entry_deleted");
exit();
