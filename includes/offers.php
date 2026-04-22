<?php

// Responsible for loading and preparing offer data
// Looks for a known offer code inside the offer description text
function find_offer_code($text)
{
    $text = strtoupper($text);

    if (strpos($text, "GRAD25") !== false) {
        return "GRAD25";
    }

    return "";
}

// Converts offer text into a numeric discount when one is mentioned
function find_offer_discount($text)
{
    $text = strtoupper($text);

    if (strpos($text, "25%") !== false) {
        return 0.25;
    }

    return null;
}

// Reads all offers from the database and prepares the values used by the homepage
function fetch_offers($db)
{
    if (!$db) {
        return [];
    }

    $result = mysqli_query($db, "SELECT offer_id, offer_title, offer_desc FROM tbl_offers ORDER BY offer_id");

    if (!$result) {
        return [];
    }

    $offers = [];

    // Build a simple array that the page can render directly
    while ($row = mysqli_fetch_assoc($result)) {
        $description = $row["offer_desc"];

        $offers[] = [
            "id" => $row["offer_id"],
            "title" => $row["offer_title"],
            "description" => $description,
            "code" => find_offer_code($description),
            "discount" => find_offer_discount($description),
        ];
    }

    mysqli_free_result($result);

    return $offers;
}

// Builds a lowercase code => discount lookup for cart and checkout validation
function fetch_offer_codes($db)
{
    $codes = [];

    foreach (fetch_offers($db) as $offer) {
        if ($offer["code"] !== "" && $offer["discount"] !== null) {
            $codes[strtolower($offer["code"])] = $offer["discount"];
        }
    }

    return $codes;
}

