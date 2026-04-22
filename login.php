<?php

// Responsible for logging users into the site
// Load shared setup and user helper functions
$db = null;
require_once __DIR__ . '/includes/bootstrap.php';

// Logged-in users should not see the login form again
if (is_logged_in()) {
    flash('auth', 'You are already logged in.');
    header('Location: index.php');
    exit;
}

$errorMessage = null;
$email = '';

// Process the submitted login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $auth = authenticate_user($db, $email, $password);

    if ($auth['success']) {
        $_SESSION['user'] = $auth['user'];
        flash('auth', 'Login successful.');
        header('Location: index.php');
        exit;
    }

    $errorMessage = (string) $auth['message'];
}

$pageTitle = 'Login | ' . SITE_NAME;

require __DIR__ . '/includes/header.php';

// Display the login form and any error returned by authenticate_user()
echo "<section class='auth-wrapper'>";
echo "<h2>Login</h2>";

if ($errorMessage !== null) {
    echo "<p class='status-message error'>" . e($errorMessage) . "</p>";
}

echo "<form class='auth-form' method='post' action='login.php'>";
echo "<label for='email'>Email</label>";
echo "<input type='email' id='email' name='email' value='" . e($email) . "' required>";
echo "<label for='password'>Password</label>";
echo "<input type='password' id='password' name='password' required>";
echo "<button type='submit'>Log In</button>";
echo "</form>";
echo "<p class='status-message'>No account yet? <a href='register.php'>Register here</a>.</p>";
echo "</section>";

require __DIR__ . '/includes/footer.php';

