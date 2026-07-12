<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$description = $_POST['description'];
$amount = $_POST['amount'];
$type = $_POST['type'];

$sql = "INSERT INTO transactions (user_id, description, amount, type) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isds", $user_id, $description, $amount, $type);
$stmt->execute();

header("Location: dashboard.php");
exit();
?>
