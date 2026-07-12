<?php
require_once __DIR__ . "/bootstrap.php";

function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.html");
        exit();
    }
}
