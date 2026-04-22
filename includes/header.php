<?php

// Responsible for the shared page header and navigation
// Use the page-specific title when one is provided, otherwise fall back to the site name
$finalPageTitle = SITE_NAME;

if (!empty($pageTitle)) {
    $finalPageTitle = $pageTitle;
}

$currentPage = basename($_SERVER["PHP_SELF"]);

$welcomeName = current_user_name();
$homeClass = "";
$productsClass = "";
$cartClass = "";
$loginClass = "";

// Mark the current page in the navigation menu
if ($currentPage === "index.php") {
    $homeClass = "active-link";
}

if ($currentPage === "products.php") {
    $productsClass = "active-link";
}

if ($currentPage === "cart.php") {
    $cartClass = "active-link";
}

if ($currentPage === "login.php") {
    $loginClass = "active-link";
}

// Output the shared page header, navigation, and welcome banner
echo "<!DOCTYPE html>";
echo "<html lang='en'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>" . e($finalPageTitle) . "</title>";
echo "<link rel='stylesheet' href='style.css'>";
echo "</head>";
echo "<body>";
echo "<header>";
echo "<h2>" . e(SITE_NAME) . "</h2>";
echo "<div>";
echo "<img class='logo' src='images/logo_reverse.png' alt='Uclan Logo'>";
echo "</div>";
echo "<nav>";
echo "<button class='hamburger' aria-label='Toggle navigation'>&#9776;</button>";
echo "<ul class='main-nav'>";
echo "<li><a class='" . $homeClass . "' href='index.php'>Home</a></li>";
echo "<li><a class='" . $productsClass . "' href='products.php'>Products</a></li>";
echo "<li><a class='" . $cartClass . "' href='cart.php'>Cart</a></li>";

if (is_logged_in()) {
    echo "<li><a href='logout.php'>Logout</a></li>";
} else {
    echo "<li><a class='" . $loginClass . "' href='login.php'>Login</a></li>";
}

echo "</ul>";
echo "</nav>";

if ($welcomeName !== "") {
    echo "<p class='welcome-banner'>Welcome back, " . e($welcomeName) . ".</p>";
}

echo "</header>";
echo "<main>";

