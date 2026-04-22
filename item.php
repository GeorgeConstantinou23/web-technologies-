<?php

// Responsible for showing one product and its reviews
// Load shared setup before reading the selected product and its reviews
$db = null;
require_once __DIR__ . '/includes/bootstrap.php';

$productId = "";

// Read the product id from the query string
if (isset($_GET["id"])) {
    $productId = trim($_GET["id"]);
}

$reviewError = null;

// Handle review submissions before the page is rendered
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["submit_review"])) {
        if (!is_logged_in()) {
            $reviewError = "You must log in before submitting a review.";
        } else {
            $reviewTitle = "";
            $comment = "";
            $rating = 0;
            $userId = "";

            if (isset($_POST["review_title"])) {
                $reviewTitle = trim($_POST["review_title"]);
            }

            if (isset($_POST["comment"])) {
                $comment = trim($_POST["comment"]);
            }

            if (isset($_POST["rating"])) {
                $rating = (int) $_POST["rating"];
            }

            if (isset($_SESSION["user"]["id"])) {
                $userId = $_SESSION["user"]["id"];
            }

            $result = add_review_for_product($db, $productId, $userId, $reviewTitle, $comment, $rating);

            if ($result["success"]) {
                flash("review", $result["message"]);
                header("Location: item.php?id=" . urlencode($productId));
                exit;
            }

            $reviewError = $result["message"];
        }
    }
}

$product = null;

// Load the selected product only when a product id was provided
if ($productId !== "") {
    $product = fetch_product_by_id($db, $productId);
}

$reviews = [];

// Reviews are only loaded when the product exists
if ($product) {
    $reviews = fetch_reviews_for_product($db, $product["id"]);
}

$ratingAverage = rating_average($reviews);
$reviewSuccess = flash("review");

$pageName = "Item";

if ($product) {
    $pageName = $product["name"];
}

$pageTitle = $pageName . " | " . SITE_NAME;

require __DIR__ . '/includes/header.php';

echo "<section>";

if ($product === null) {
    echo "<p class='status-message error'>Product not found. Check the product ID or database records.</p>";
    echo "<p><a class='back-link' href='products.php'>Back to products</a></p>";
} else {
    // Prepare the add-to-cart controls for the selected item
    $quantityInputId = "quantity_" . $product["id"];
    $statusClass = product_status_class($product["status"]);
    $buttonText = "Add to Cart";
    $disabled = "";

    if ($statusClass === "out-of-stock") {
        $buttonText = "Out of Stock";
        $disabled = " disabled";
    }

    echo "<div class='product-details-container " . e($statusClass) . "'>";
    echo "<h1>" . e($product["name"]) . "</h1>";
    echo "<img src='" . e($product["image"]) . "' alt='" . e($product["name"]) . "'>";
    echo "<p>Price: &pound;" . e(format_price($product["price"])) . "</p>";
    echo "<p>" . e($product["description"]) . "</p>";
    echo "<p>Status: " . e($product["status"]) . "</p>";
    echo "<label for='" . e($quantityInputId) . "'>Quantity:</label>";
    echo "<input type='number' id='" . e($quantityInputId) . "' min='1' value='1'>";
    echo "<button class='add-to-cart' type='button'"
        . " data-id='" . e($product["id"]) . "'"
        . " data-name='" . e($product["name"]) . "'"
        . " data-price='" . e($product["price"]) . "'"
        . " data-image='" . e($product["image"]) . "'"
        . " data-quantity-target='" . e($quantityInputId) . "'"
        . $disabled
        . ">"
        . e($buttonText)
        . "</button>";
    echo "<a href='products.php' class='back-link'>Back to products</a>";
    echo "</div>";

    // Display existing reviews and the review form for logged-in users
    echo "<section class='reviews-section'>";
    echo "<h2>Reviews</h2>";

    if ($reviewSuccess !== null) {
        echo "<p class='status-message success'>" . e($reviewSuccess) . "</p>";
    }

    if ($reviewError !== null) {
        echo "<p class='status-message error'>" . e($reviewError) . "</p>";
    }

    if ($ratingAverage !== null) {
        echo "<p><strong>Average rating:</strong> " . e(number_format($ratingAverage, 1)) . " / 5</p>";
    }

    if ($reviews === []) {
        echo "<p>No reviews yet for this item.</p>";
    } else {
        echo "<div class='review-list'>";

        // Render each review card with reviewer, rating, and date details
        foreach ($reviews as $review) {
            $reviewTitleText = "";
            $reviewRating = 0;
            $reviewDate = "";

            if (isset($review["title"])) {
                $reviewTitleText = $review["title"];
            }

            if (isset($review["rating"])) {
                $reviewRating = $review["rating"];
            }

            if (isset($review["created_at"])) {
                $reviewDate = $review["created_at"];
            }

            echo "<div class='review-card'>";

            if ($reviewTitleText !== "") {
                echo "<p><strong>" . e($reviewTitleText) . "</strong></p>";
            }

            echo "<p>By " . e($review["reviewer"]) . "</p>";

            if ($reviewRating > 0) {
                echo "<p>Rating: " . e($reviewRating) . "/5</p>";
            }

            echo "<p>" . e($review["comment"]) . "</p>";

            if ($reviewDate !== "") {
                echo "<p class='review-date'>" . e($reviewDate) . "</p>";
            }

            echo "</div>";
        }

        echo "</div>";
    }

    if (is_logged_in()) {
        echo "<form class='review-form' method='post' action='item.php?id=" . urlencode($productId) . "'>";
        echo "<label for='review_title'>Review title</label>";
        echo "<input type='text' id='review_title' name='review_title' maxlength='255' required>";
        echo "<label for='rating'>Rating (1 to 5)</label>";
        echo "<input type='number' id='rating' name='rating' min='1' max='5' value='5' required>";
        echo "<label for='comment'>Your review</label>";
        echo "<textarea id='comment' name='comment' rows='4' required></textarea>";
        echo "<button type='submit' name='submit_review'>Submit Review</button>";
        echo "</form>";
    } else {
        echo "<p><a href='login.php'>Log in</a> to leave a review.</p>";
    }

    echo "</section>";
}

echo "</section>";

require __DIR__ . '/includes/footer.php';

