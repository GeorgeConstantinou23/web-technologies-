<?php

// Responsible for shared helper functions used across the site
// Escapes output so values can be printed safely inside HTML
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// Checks whether a logged-in user record exists in the current session
function is_logged_in()
{
    if (empty($_SESSION['user'])) {
        return false;
    }

    return is_array($_SESSION['user']);
}

// Returns the current logged-in user's name, or an empty string for guests
function current_user_name()
{
    if (!is_logged_in()) {
        return '';
    }

    return (string) ($_SESSION['user']['name'] ?? '');
}

// Stores a one-time flash message or reads and clears an existing one
function flash($key, $message = null)
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }

    if (!isset($_SESSION['flash'][$key])) {
        return null;
    }

    $value = (string) $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);

    return $value;
}

// Formats a value as a price string with two decimal places
function format_price($price)
{
    return number_format($price, 2);
}

// Converts the database stock value into the CSS class used by the product cards
function product_status_class($status)
{
    $status = strtolower(trim($status));

    if ($status === "out-of-stock") {
        return "out-of-stock";
    }

    if ($status === "low-stock") {
        return "last-few";
    }

    return "in-stock";
}
