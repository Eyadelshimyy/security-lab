<?php
require "bootstrap.php";

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    header("Location: login.html?error=invalid");
    exit();
}

$sql = "select * from users where username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$user = $result->num_rows > 0 ? $result->fetch_assoc() : null;

// Same generic message whether the username doesn't exist or the password is
// wrong, so a visitor can't use this form to enumerate registered usernames.
if (!$user || !password_verify($password, $user['password'])) {
    header("Location: login.html?error=invalid");
    exit();
}

session_regenerate_id(true);
$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['email'] = $user['email'];
header("Location: home.php");
exit();
