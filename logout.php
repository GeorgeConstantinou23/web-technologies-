<?php

// Responsible for logging users out of the site
// Load shared setup so the current session can be updated
require_once __DIR__ . '/includes/bootstrap.php';

// Remove the logged-in user and return to the homepage
unset($_SESSION['user']);
flash('auth', 'You have been logged out.');
header('Location: index.php');
exit;

