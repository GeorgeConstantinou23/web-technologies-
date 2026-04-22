<?php

// Responsible for creating new user accounts
// Load shared setup and registration helpers
$db = null;
require_once __DIR__ . '/includes/bootstrap.php';

// Logged-in users do not need to create another account
if (is_logged_in()) {
    flash('auth', 'You are already logged in.');
    header('Location: index.php');
    exit;
}

$errorMessage = null;
$name = '';
$email = '';
$address = '';

// Process the submitted registration form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $address = trim((string) ($_POST['address'] ?? ''));
    $registration = register_user($db, $name, $email, $password, $address);

    if ($registration['success']) {
        flash('auth', (string) $registration['message']);
        header('Location: login.php');
        exit;
    }

    $errorMessage = (string) $registration['message'];
}

$pageTitle = 'Register | ' . SITE_NAME;

require __DIR__ . '/includes/header.php';

// Display the registration form and any validation message
echo "<section class='auth-wrapper'>";
echo "<h2>Register</h2>";

if ($errorMessage !== null) {
    echo "<p class='status-message error'>" . e($errorMessage) . "</p>";
}

echo "<form class='auth-form' method='post' action='register.php'>";
echo "<label for='name'>Full name</label>";
echo "<input type='text' id='name' name='name' value='" . e($name) . "' required>";
echo "<label for='email'>Email</label>";
echo "<input type='email' id='email' name='email' value='" . e($email) . "' required>";
echo "<label for='password'>Password</label>";
echo "<input type='password' id='password' name='password' minlength='6' required>";
echo "<label for='address'>Address</label>";
echo "<input type='text' id='address' name='address' value='" . e($address) . "' required>";
echo "<button type='submit'>Create Account</button>";
echo "</form>";
echo "<p class='status-message'>Already registered? <a href='login.php'>Log in here</a>.</p>";
echo "</section>";

require __DIR__ . '/includes/footer.php';

