<?php
require "auth.php";
require "csrf.php";
require_login();
csrf_verify();

$user_id = $_SESSION['user_id'];
$note_id = (int) ($_POST['note_id'] ?? 0);

$stmt = $conn->prepare("DELETE FROM ctf_notes WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $note_id, $user_id);
$stmt->execute();

header("Location: ctf.php?msg=ctf_deleted");
exit();
