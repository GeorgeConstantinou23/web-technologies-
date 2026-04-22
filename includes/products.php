<?php

// Responsible for loading product data from the database
// Loads all store products from tbl_products for the catalogue page
function fetch_products($db)
{
    if (!$db) {
        return [];
    }

    $result = mysqli_query($db, "SELECT product_id, product_title, product_price, product_stock, product_src, product_desc FROM tbl_products ORDER BY product_id");

    if (!$result) {
        return [];
    }

    $products = [];

    // Convert each database row into a simpler product array used by the pages
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = [
            "id" => (string) $row["product_id"],
            "name" => (string) $row["product_title"],
            "price" => (float) $row["product_price"],
            "description" => (string) $row["product_desc"],
            "image" => (string) $row["product_src"],
            "status" => (string) $row["product_stock"],
        ];
    }

    mysqli_free_result($result);

    return $products;
}

// Loads one product by id for the item details page
function fetch_product_by_id($db, $productId)
{
    if (!$db) {
        return null;
    }

    $productIdInt = (int) $productId;
    $statement = mysqli_prepare(
        $db,
        "SELECT product_id, product_title, product_price, product_stock, product_src, product_desc
         FROM tbl_products
         WHERE product_id = ?
         LIMIT 1"
    );

    if (!$statement) {
        return null;
    }

    mysqli_stmt_bind_param($statement, "i", $productIdInt);

    if (!mysqli_stmt_execute($statement)) {
        mysqli_stmt_close($statement);
        return null;
    }

    $result = mysqli_stmt_get_result($statement);
    $row = null;

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
    }

    mysqli_stmt_close($statement);

    if (!$row) {
        return null;
    }

    return [
        "id" => (string) $row["product_id"],
        "name" => (string) $row["product_title"],
        "price" => (float) $row["product_price"],
        "description" => (string) $row["product_desc"],
        "image" => (string) $row["product_src"],
        "status" => (string) $row["product_stock"],
    ];
}

