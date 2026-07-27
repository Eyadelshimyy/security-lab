<?php
require "auth.php";
require "csrf.php";
require_login();
csrf_verify();

$user_id = $_SESSION['user_id'];
$roomName = trim($_POST['room_name'] ?? '');
$platform = trim($_POST['platform'] ?? '') ?: 'Other';
$category = trim($_POST['category'] ?? '') ?: 'Misc';

$validPlatforms = ['HackTheBox', 'TryHackMe', 'CTFtime', 'PicoCTF', 'Other'];
$validCategories = ['Web', 'Pwn', 'Reversing', 'Crypto', 'Forensics', 'Network', 'OSINT', 'Misc'];

if ($roomName === '' || strlen($roomName) > 150 || !in_array($platform, $validPlatforms, true) || !in_array($category, $validCategories, true)) {
    header("Location: ctf.php?msg=ctf_error");
    exit();
}

$stmt = $conn->prepare("INSERT INTO ctf_notes (user_id, room_name, platform, category) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $user_id, $roomName, $platform, $category);
$stmt->execute();

header("Location: ctf.php?msg=ctf_added");
exit();
