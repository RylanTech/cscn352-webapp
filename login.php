<?php
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = is_string($_POST['username'] ?? null) ? trim($_POST['username']) : '';
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';

    if ($username && $password) {
        try {
            $stmt = getDB()->prepare(
                'SELECT id, username, password_hash FROM users WHERE username = ?'
            );
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int) $user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: pizza.php');
                exit;
            }
            $error = 'Invalid username or password.';
        } catch (Throwable $e) {
            error_log('Login failed: ' . $e->getMessage());
            $error = 'Login is temporarily unavailable.';
        }
    } else {
        $error = 'Please enter both username and password.';
    }
}

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: pizza.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pizza App - Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container login-box">
        <h1>🍕 Pizza Server Login</h1>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" autocomplete="username" required autofocus>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" autocomplete="current-password" required>

            <button type="submit">Login</button>
        </form>
        <p class="hint">New here? <a href="register.php">Create an account</a>.</p>
    </div>
</body>
</html>