<?php
require "auth.php";
require "csrf.php";
require_login();
csrf_verify();

$user_id = $_SESSION['user_id'];
$description = trim($_POST['description'] ?? '');
$amount = $_POST['amount'] ?? '';
$type = $_POST['type'] ?? '';

$validAmount = is_numeric($amount) && (float) $amount > 0;
$validType = in_array($type, ['income', 'expense'], true);

if ($description === '' || strlen($description) > 255 || !$validAmount || !$validType) {
    header("Location: finance.php?msg=transaction_error");
    exit();
}

$amount = round((float) $amount, 2);

$sql = "INSERT INTO transactions (user_id, description, amount, type) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isds", $user_id, $description, $amount, $type);
$stmt->execute();

header("Location: finance.php?msg=transaction_added");
exit();
