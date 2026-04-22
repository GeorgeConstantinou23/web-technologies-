<?php

// Responsible for loading and saving product reviews
// Loads all reviews for one product and joins the reviewer name from tbl_users
function fetch_reviews_for_product($db, $productId)
{
    if (!$db) {
        return [];
    }

    $productIdInt = (int) $productId;
    $statement = mysqli_prepare(
        $db,
        "SELECT r.review_title, r.review_desc, r.review_rating, r.review_timestamp, u.user_name
         FROM tbl_reviews r
         LEFT JOIN tbl_users u ON u.user_id = r.user_id
         WHERE r.product_id = ?
         ORDER BY r.review_timestamp DESC"
    );

    if (!$statement) {
        return [];
    }

    mysqli_stmt_bind_param($statement, "i", $productIdInt);

    if (!mysqli_stmt_execute($statement)) {
        mysqli_stmt_close($statement);
        return [];
    }

    $result = mysqli_stmt_get_result($statement);
    $reviews = [];

    // Prepare review cards with safe default values for missing user names
    while ($row = mysqli_fetch_assoc($result)) {
        $reviewer = "Anonymous";

        if ((string) $row["user_name"] !== "") {
            $reviewer = (string) $row["user_name"];
        }

        $reviews[] = [
            "title" => (string) $row["review_title"],
            "reviewer" => $reviewer,
            "rating" => (int) $row["review_rating"],
            "comment" => (string) $row["review_desc"],
            "created_at" => (string) $row["review_timestamp"],
        ];
    }

    mysqli_free_result($result);
    mysqli_stmt_close($statement);

    return $reviews;
}

// Calculates the average rating shown on the item page
function rating_average($reviews)
{
    $total = 0;
    $count = 0;

    foreach ($reviews as $review) {
        $rating = (int) ($review["rating"] ?? 0);

        if ($rating > 0) {
            $total += $rating;
            $count++;
        }
    }

    if ($count === 0) {
        return null;
    }

    return $total / $count;
}

// Validates and saves a new review for the selected product
function add_review_for_product($db, $productId, $userId, $reviewTitle, $comment, $rating)
{
    if (!$db) {
        return ["success" => false, "message" => "Database connection is unavailable."];
    }

    $reviewTitle = trim($reviewTitle);
    $comment = trim($comment);
    $productIdInt = (int) $productId;
    $userIdInt = (int) $userId;

    if ($reviewTitle === "") {
        return ["success" => false, "message" => "Review title is required."];
    }

    if ($comment === "") {
        return ["success" => false, "message" => "Review text is required."];
    }

    if ($rating < 1 || $rating > 5) {
        return ["success" => false, "message" => "Rating must be between 1 and 5."];
    }

    if ($productIdInt <= 0 || $userIdInt <= 0) {
        return ["success" => false, "message" => "Invalid user or product."];
    }

    $statement = mysqli_prepare(
        $db,
        "INSERT INTO tbl_reviews (user_id, product_id, review_title, review_desc, review_rating)
         VALUES (?, ?, ?, ?, ?)"
    );

    if (!$statement) {
        return ["success" => false, "message" => "Unable to save review."];
    }

    mysqli_stmt_bind_param($statement, "iissi", $userIdInt, $productIdInt, $reviewTitle, $comment, $rating);
    $success = mysqli_stmt_execute($statement);
    mysqli_stmt_close($statement);

    if (!$success) {
        return ["success" => false, "message" => "Unable to save review."];
    }

    return ["success" => true, "message" => "Review submitted successfully."];
}

