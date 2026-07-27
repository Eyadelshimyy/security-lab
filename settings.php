<?php
require "auth.php";
require "csrf.php";
require_login();

$displayName = htmlspecialchars($_SESSION['username']);
$initial = strtoupper(substr($_SESSION['username'], 0, 1));
$currentEmail = htmlspecialchars($_SESSION['email'] ?? '');

$activeNav = 'settings';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Account Settings</title>
    <link rel="stylesheet" href="dashboard-style.css">
    <script>(function(){try{if(localStorage.getItem('theme')==='red'){document.documentElement.setAttribute('data-theme','red');}}catch(e){}})();</script>
</head>
<body>
    <div class="app-shell">
        <?php require "nav.php"; ?>

        <div class="main-area">
            <?php require "topbar.php"; ?>

            <div class="bento-grid" style="grid-template-columns: 1fr 1fr;">
                <div class="card">
                    <div class="card-header">
                        <h2>Username</h2>
                    </div>
                    <div class="insight-desc" style="margin-bottom:14px;">Currently <b style="color:var(--text);"><?php echo $displayName; ?></b></div>
                    <form action="update-username.php" method="POST" style="display:flex; flex-direction:column; gap:10px;">
                        <?php csrf_field(); ?>
                        <input type="text" name="new_username" class="field" placeholder="New username" required maxlength="50">
                        <input type="password" name="current_password" class="field" placeholder="Current password" autocomplete="current-password" required>
                        <button type="submit" class="btn-add" style="align-self:flex-start;">Update Username</button>
                    </form>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2>Email</h2>
                    </div>
                    <div class="insight-desc" style="margin-bottom:14px;">Currently <b style="color:var(--text);"><?php echo $currentEmail; ?></b></div>
                    <form action="update-email.php" method="POST" style="display:flex; flex-direction:column; gap:10px;">
                        <?php csrf_field(); ?>
                        <input type="email" name="new_email" class="field" placeholder="New email" required>
                        <input type="password" name="current_password" class="field" placeholder="Current password" autocomplete="current-password" required>
                        <button type="submit" class="btn-add" style="align-self:flex-start;">Update Email</button>
                    </form>
                </div>
            </div>

            <div class="card" style="max-width:520px;">
                <div class="card-header">
                    <h2>Password</h2>
                </div>
                <div class="insight-desc" style="margin-bottom:14px;">Choose a new password of at least 8 characters.</div>
                <form action="update-password.php" method="POST" style="display:flex; flex-direction:column; gap:10px;">
                    <?php csrf_field(); ?>
                    <input type="password" name="current_password" class="field" placeholder="Current password" autocomplete="current-password" required>
                    <input type="password" name="new_password" class="field" placeholder="New password" autocomplete="new-password" minlength="8" required>
                    <input type="password" name="confirm_password" class="field" placeholder="Confirm new password" autocomplete="new-password" minlength="8" required>
                    <button type="submit" class="btn-add" style="align-self:flex-start;">Update Password</button>
                </form>
            </div>
        </div>
    </div>
    <script src="app.js"></script>
</body>
</html>
