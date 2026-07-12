<?php
require "auth.php";
require "csrf.php";
require_login();
csrf_verify();

$user_id = $_SESSION['user_id'];
$transaction_id = (int) ($_POST['transaction_id'] ?? 0);

$stmt = $conn->prepare("DELETE FROM transactions WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $transaction_id, $user_id);
$stmt->execute();

header("Location: finance.php?msg=transaction_deleted");
exit();
