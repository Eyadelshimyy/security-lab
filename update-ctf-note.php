<?php
require "auth.php";
require "csrf.php";
require_login();
csrf_verify();

$user_id = $_SESSION['user_id'];
$note_id = (int) ($_POST['note_id'] ?? 0);
$status = $_POST['status'] ?? '';
$notes = trim($_POST['notes'] ?? '');

$validStatuses = ['not_started', 'in_progress', 'solved'];
if (!in_array($status, $validStatuses, true) || strlen($notes) > 10000) {
    header("Location: ctf.php?msg=ctf_error");
    exit();
}

$stmt = $conn->prepare("UPDATE ctf_notes SET status = ?, notes = ? WHERE id = ? AND user_id = ?");
$stmt->bind_param("ssii", $status, $notes, $note_id, $user_id);
$stmt->execute();

header("Location: ctf.php?msg=ctf_updated");
exit();
