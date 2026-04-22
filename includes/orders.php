<?php

// Responsible for checkout totals and order creation
// Saves the current cart as an order record in tbl_orders
function create_order_from_cart($db, $userId, $cartItems)
{
    if (!$db) {
        return ["success" => false, "message" => "Database connection is unavailable."];
    }

    if ($userId <= 0) {
        return ["success" => false, "message" => "You must be logged in to place an order."];
    }

    if ($cartItems === []) {
        return ["success" => false, "message" => "Your cart is empty."];
    }

    $productsById = [];

    foreach (fetch_products($db) as $product) {
        $productsById[(int) $product["id"]] = $product;
    }

    $productIds = [];

    // Expand quantities into the comma-separated product id list stored in tbl_orders
    foreach ($cartItems as $item) {
        if (!is_array($item)) {
            continue;
        }

        $productId = (int) ($item["id"] ?? 0);
        $quantity = (int) ($item["quantity"] ?? 1);

        if ($productId <= 0 || !isset($productsById[$productId])) {
            continue;
        }

        if ($quantity < 1) {
            $quantity = 1;
        }

        if (product_status_class($productsById[$productId]["status"]) === "out-of-stock") {
            return ["success" => false, "message" => "One or more items in your cart are out of stock"];
        }

        for ($i = 0; $i < $quantity; $i++) {
            $productIds[] = $productId;
        }
    }

    if ($productIds === []) {
        return ["success" => false, "message" => "No valid products were found in the cart."];
    }

    $productIdsText = implode(",", $productIds);
    $insert = mysqli_prepare($db, "INSERT INTO tbl_orders (user_id, product_ids) VALUES (?, ?)");

    if (!$insert) {
        return ["success" => false, "message" => "Unable to place order."];
    }

    mysqli_stmt_bind_param($insert, "is", $userId, $productIdsText);
    $success = mysqli_stmt_execute($insert);
    $orderId = 0;

    if ($success) {
        $orderId = (int) mysqli_insert_id($db);
    }

    mysqli_stmt_close($insert);

    if (!$success) {
        return ["success" => false, "message" => "Unable to place order."];
    }

    return ["success" => true, "message" => "Order placed successfully.", "order_id" => $orderId];
}

// Recalculates totals on the server and validates any submitted offer code
function calculate_cart_summary($db, $cartItems, $offerCode = "")
{
    if (!$db) {
        return ["success" => false, "message" => "Database connection is unavailable."];
    }

    if ($cartItems === []) {
        return ["success" => false, "message" => "Your cart is empty."];
    }

    $productsById = [];

    foreach (fetch_products($db) as $product) {
        $productsById[(int) $product["id"]] = $product;
    }

    $subtotal = 0.0;
    $hasValidItem = false;

    // Rebuild the subtotal from trusted database prices instead of cookie values
    foreach ($cartItems as $item) {
        if (!is_array($item)) {
            continue;
        }

        $productId = (int) ($item["id"] ?? 0);
        $quantity = (int) ($item["quantity"] ?? 1);

        if ($quantity < 1) {
            $quantity = 1;
        }

        if ($productId <= 0 || !isset($productsById[$productId])) {
            continue;
        }

        if (product_status_class($productsById[$productId]["status"]) === "out-of-stock") {
            return ["success" => false, "message" => "One or more items in your cart are out of stock"];
        }

        $subtotal += (float) $productsById[$productId]["price"] * $quantity;

        $hasValidItem = true;
    }

    if (!$hasValidItem) {
        return ["success" => false, "message" => "No valid products were found in the cart."];
    }

    $offerCode = strtoupper(trim($offerCode));
    $discountRate = 0.0;
    $offerValid = false;

    if ($offerCode !== "") {
        $offerCodes = fetch_offer_codes($db);
        $lookupKey = strtolower($offerCode);

        if (!isset($offerCodes[$lookupKey])) {
            return ["success" => false, "message" => "Offer code is invalid (server-side verification failed)."];
        }

        $discountRate = (float) $offerCodes[$lookupKey];
        $offerValid = true;
    }

    $discountAmount = $subtotal * $discountRate;
    $total = $subtotal - $discountAmount;
    $savedOfferCode = "";

    if ($offerValid) {
        $savedOfferCode = $offerCode;
    }

    return [
        "success" => true,
        "offer_valid" => $offerValid,
        "offer_code" => $savedOfferCode,
        "discount_rate" => $discountRate,
        "subtotal" => round($subtotal, 2),
        "discount_amount" => round($discountAmount, 2),
        "total" => round($total, 2),
    ];
}

