<?php

// Responsible for the homepage media and current offers
// Load shared setup, the database connection, and helper functions first
$db = null;
require_once __DIR__ . '/includes/bootstrap.php';

// Prepare homepage data before any HTML is sent to the browser
$pageTitle = 'Home | ' . SITE_NAME;
$offers = fetch_offers($db);
$authMessage = flash('auth');

require __DIR__ . '/includes/header.php';

// Show one-time success messages such as login feedback
if ($authMessage !== null) {
    echo "<p class='status-message success'>" . e($authMessage) . "</p>";
}

// Display the embedded media shown on the homepage
echo "<section class='media-section'>";
echo "<iframe width='480' height='300' src='https://player.vimeo.com/video/1071072056?h=d4263dcc56' title='UClan introduction video' allowfullscreen></iframe>";
echo "<video controls width='480' height='300'>";
echo "<source src='video.mp4' type='video/mp4'>";
echo "</video>";
echo "</section>";

// Display the current store offers from the database
echo "<section class='offer-section'>";
echo "<h2>Current Offers</h2>";

if (!$db) {
    echo "<p class='status-message error'>Database is not connected yet. Update credentials in <code>includes/bootstrap.php</code>.</p>";
} elseif ($offers === []) {
    echo "<p class='status-message'>No offers found in <code>tbl_offers</code>.</p>";
} else {
    echo "<div class='offer-grid'>";

    // Render each offer card with its code and discount text when available
    foreach ($offers as $offer) {
        echo "<div class='offer-card'>";
        echo "<h3>" . e($offer['title']) . "</h3>";
        echo "<p>" . e($offer['description']) . "</p>";

        if ($offer['code'] !== '') {
            echo "<p><strong>Code:</strong> " . e($offer['code']) . "</p>";
        }

        if ($offer['discount'] !== null) {
            $discount = (float) $offer['discount'];

            if ($discount > 1) {
                $discountText = number_format($discount, 0) . '% off';
            } else {
                $discountText = number_format($discount * 100, 0) . '% off';
            }

            echo "<p><strong>" . e($discountText) . "</strong></p>";
        }

        echo "</div>";
    }

    echo "</div>";
}

echo "</section>";

require __DIR__ . '/includes/footer.php';

