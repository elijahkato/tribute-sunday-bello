<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $usernameMatches = hash_equals(ADMIN_USERNAME, $username);
    $passwordMatches = password_verify($password, ADMIN_PASSWORD_HASH);

    if ($usernameMatches && $passwordMatches) {
        session_regenerate_id(true);
        $_SESSION['is_admin'] = true;
        header('Location: dashboard.php');
        exit;
    }
    $error = 'Incorrect username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="content-wrapper" style="max-width:420px; margin-top:5rem;">
        <div class="upload-box">
            <h3>Admin Login</h3>
            <?php if ($error): ?>
                <p style="color:#b03030; font-size:0.9rem; margin-top:-0.5rem;"><?= h($error) ?></p>
            <?php endif; ?>
            <form method="POST" class="tribute-form">
                <label class="field-label" for="username">Username</label>
                <input type="text" id="username" name="username" required autofocus autocomplete="username">

                <label class="field-label" for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
                <button type="submit" class="submit-btn">Log In</button>
            </form>
        </div>
    </div>
</body>
</html>
