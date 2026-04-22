<?php

// Responsible for session setup, shared includes, and the database connection
if (session_status() === PHP_SESSION_NONE) { // Checks whether a PHP session has not started yet
    session_start(); // Starts the session so login and flash messages can work
}

define('DB_HOST', 'localhost'); // Stores the database server host name
define('DB_PORT', 3306); // Stores the database server port
define('DB_NAME', 'gconstantinou'); // Stores the database name
define('DB_USER', 'gconstantinou'); // Stores the database username
define('DB_PASS', 'gP5JCQk82b'); // Stores the database password
define('SITE_NAME', 'UClan Web Page'); // Stores the site name used in page titles and headers

require_once __DIR__ . '/helpers.php'; // Loads shared helper functions
require_once __DIR__ . '/offers.php'; // Loads offer-related functions
require_once __DIR__ . '/products.php'; // Loads product-related functions
require_once __DIR__ . '/users.php'; // Loads user and login functions
require_once __DIR__ . '/reviews.php'; // Loads review functions
require_once __DIR__ . '/orders.php'; // Loads order and cart summary functions

$db = null; // Holds the database connection for the page

mysqli_report(MYSQLI_REPORT_OFF); // Stops raw mysqli warnings from showing on the page

$db = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT); // Connects to the MySQL database

if ($db) {
    mysqli_set_charset($db, "utf8mb4"); // Sets UTF-8 encoding when the connection works
}
