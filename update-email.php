<?php
require "auth.php";
require "csrf.php";
require_login();
csrf_verify();

$user_id = $_SESSION['user_id'];
$newEmail = trim($_POST['new_email'] ?? '');
$currentPassword = $_POST['current_password'] ?? '';

if ($newEmail === '' || filter_var($newEmail, FILTER_VALIDATE_EMAIL) === false) {
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
    $update = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
    $update->bind_param("si", $newEmail, $user_id);
    $update->execute();
    $_SESSION['email'] = $newEmail;
    header("Location: settings.php?msg=profile_updated");
    exit();
} catch (mysqli_sql_exception $e) {
    header("Location: settings.php?msg=profile_error");
    exit();
}
