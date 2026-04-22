<?php

// Responsible for displaying the cart and offer code area
// Load shared setup and cart-related database data
$db = null;
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Cart | ' . SITE_NAME;
$offers = fetch_offers($db);
$offerCodes = fetch_offer_codes($db);
$visibleCodes = [];
$checkoutMessage = flash("checkout");

// Keep only offers that have a visible code to show on the cart page
foreach ($offers as $offer) {
    if ($offer["code"] !== "") {
        $visibleCodes[] = $offer;
    }
}

require __DIR__ . '/includes/header.php';

// Build the cart page shell. cart.js fills in the cart items and totals
echo "<section>";
echo "<h2>Your Cart</h2>";

if (!is_logged_in()) {
    echo "<p class='status-message'>You can build a cart as a guest, but checkout requires login.</p>";
}

echo "<div id='cart-items'></div>";
echo "<div id='cart-total'></div>";

if ($checkoutMessage !== null) {
    echo "<p class='status-message'>" . e($checkoutMessage) . "</p>";
}

echo "<form method='post' action='checkout.php'>";
echo "<div id='offer'>";
echo "<label for='offer-code'>Offer Code:</label>";
echo "<input type='text' name='offer-code' id='offer-code'>";
echo "<button id='apply-offer' type='button'>Apply</button>";
echo "<p id='offer-message'></p>";
echo "</div>";

if ($visibleCodes !== []) {
    echo "<div class='offer-hints'>";
    echo "<p><strong>Available codes:</strong></p>";
    echo "<ul>";

    // Show the shopper which codes are valid and what each one gives
    foreach ($visibleCodes as $offer) {
        echo "<li>";
        echo "<code>" . e((string) $offer['code']) . "</code>";

        if ($offer['discount'] !== null) {
            $discount = (float) $offer['discount'];

            if ($discount > 1) {
                $discountText = number_format($discount, 0) . '%';
            } else {
                $discountText = number_format($discount * 100, 0) . '%';
            }

            echo " - " . e($discountText) . " off";
        }

        echo "</li>";
    }

    echo "</ul>";
    echo "</div>";
}

echo "<div id='checkout-area'>";
echo "<button id='checkout-btn' type='submit'>Buy Now</button>";
echo "</div>";
echo "</form>";

echo "<div id='clear'>";
echo "<button id='clear-cart' type='button'>Clear Cart</button>";
echo "</div>";
echo "</section>";

echo "<script>";
echo "window.offerCodes = {};";

// Pass the valid offer codes to JavaScript for client-side feedback
foreach ($offerCodes as $code => $discountValue) {
    echo "window.offerCodes['" . $code . "'] = " . $discountValue . ";";
}

echo "</script>";

require __DIR__ . '/includes/footer.php';

