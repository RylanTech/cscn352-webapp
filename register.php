<?php
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: pizza.php');
    exit;
}

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = is_string($_POST['username'] ?? null) ? trim($_POST['username']) : '';
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
    $confirmPassword = is_string($_POST['confirm_password'] ?? null)
        ? $_POST['confirm_password']
        : '';

    if (!preg_match('/^[A-Za-z0-9_]{3,32}$/', $username)) {
        $error = 'Username must be 3–32 characters using letters, numbers, or underscores.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        try {
            $pdo = getDB();
            $stmt = $pdo->prepare(
                'INSERT INTO users (username, password_hash) VALUES (?, ?)'
            );
            $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $pdo->lastInsertId();
            $_SESSION['username'] = $username;
            header('Location: pizza.php');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $error = 'That username is already taken.';
            } else {
                error_log('Account registration failed: ' . $e->getMessage());
                $error = 'Account creation is temporarily unavailable.';
            }
        } catch (Throwable $e) {
            error_log('Account registration failed: ' . $e->getMessage());
            $error = 'Account creation is temporarily unavailable.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pizza App - Create Account</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container login-box">
        <h1>Create an Account</h1>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <form method="POST" action="register.php">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" minlength="3" maxlength="32"
                   pattern="[A-Za-z0-9_]+" value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>"
                   autocomplete="username" required autofocus>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" minlength="8"
                   autocomplete="new-password" required>

            <label for="confirm_password">Confirm password</label>
            <input type="password" id="confirm_password" name="confirm_password"
                   minlength="8" autocomplete="new-password" required>

            <button type="submit">Create Account</button>
        </form>
        <p class="hint">Already have an account? <a href="login.php">Log in</a>.</p>
    </div>
</body>
</html>
