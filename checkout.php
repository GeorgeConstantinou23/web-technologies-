<?php

// Responsible for validating the cart and creating an order
// Load shared setup so checkout can validate the cart and create an order
$db = null;
require_once __DIR__ . '/includes/bootstrap.php';

// Checkout only accepts POST requests from the cart form
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: cart.php");
    exit;
}

// Only logged-in users are allowed to place an order
if (!is_logged_in()) {
    flash("auth", "Please log in before checkout.");
    header("Location: login.php");
    exit;
}

$cookieCartRaw = "";

// Read the cart cookie so the server can verify what is being purchased
if (isset($_COOKIE["cart"])) {
    $cookieCartRaw = $_COOKIE["cart"];
}

if (!$db) {
    flash("checkout", "Database connection unavailable.");
    header("Location: cart.php");
    exit;
}

$cartItems = array();

// Convert the stored cart cookie back into a PHP array
if ($cookieCartRaw !== "") {
    $decoded = urldecode($cookieCartRaw);
    $cartItems = json_decode($decoded, true);

    if (!is_array($cartItems)) {
        $cartItems = array();
    }
}

$offerCode = "";

// Read the submitted offer code so it can be checked again on the server
if (isset($_POST["offer-code"])) {
    $offerCode = trim($_POST["offer-code"]);
}

if ($cartItems === []) {
    flash("checkout", "Your cart is empty.");
    header("Location: cart.php");
    exit;
}

$summary = calculate_cart_summary($db, $cartItems, $offerCode);

if (!$summary["success"]) {
    flash("checkout", $summary["message"]);
    header("Location: cart.php");
    exit;
}

$userId = 0;

// Use the logged-in user's id when writing the order to the database
if (isset($_SESSION["user"]["id"])) {
    $userId = (int) $_SESSION["user"]["id"];
}

$orderResult = create_order_from_cart($db, $userId, $cartItems);

if (!$orderResult["success"]) {
    flash("checkout", $orderResult["message"]);
    header("Location: cart.php");
    exit;
}

setcookie("cart", "", time() - 3600, "/");

// Build the final checkout message shown back on the cart page
$offerMessage = "";

if ($summary["offer_valid"]) {
    $offerMessage = " Offer verified (" . $summary["offer_code"] . ").";
}

$message = "Thank you for your custom. Order #" . $orderResult["order_id"] . " created successfully." . $offerMessage;

flash("checkout", $message);
header("Location: cart.php");
exit;

