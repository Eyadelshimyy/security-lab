<?php
require "bootstrap.php";

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$usernameValid = $username !== '' && mb_strlen($username) <= 50;
$emailValid = $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
$passwordValid = mb_strlen($password) >= 8;

if (!$usernameValid || !$emailValid) {
    header("Location: register.html?error=invalid");
    exit();
}

if (!$passwordValid) {
    header("Location: register.html?error=weakpass");
    exit();
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $username, $hashedPassword, $email);

try {
    $stmt->execute();
    header("Location: login.html?registered=1");
    exit();
} catch (mysqli_sql_exception $e) {
    if ($conn->errno === 1062) {
        header("Location: register.html?error=duplicate");
        exit();
    }
    header("Location: register.html?error=server_error");
    exit();
}
