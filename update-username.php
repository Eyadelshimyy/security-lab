<?php
require "auth.php";
require "csrf.php";
require_login();
csrf_verify();

$user_id = $_SESSION['user_id'];
$newUsername = trim($_POST['new_username'] ?? '');
$currentPassword = $_POST['current_password'] ?? '';

if ($newUsername === '' || strlen($newUsername) > 50) {
    header("Location: settings.php?msg=profile_error");
    exit();
}

$stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user || !password_verify($currentPassword, $user['password'])) {
    header("Location: settings.php?msg=profile_error");
    exit();
}

try {
    $update = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
    $update->bind_param("si", $newUsername, $user_id);
    $update->execute();
    $_SESSION['username'] = $newUsername;
    header("Location: settings.php?msg=profile_updated");
    exit();
} catch (mysqli_sql_exception $e) {
    header("Location: settings.php?msg=profile_error");
    exit();
}
