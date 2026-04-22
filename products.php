<?php

// Responsible for listing all products in the store
// Load shared setup so the page can read products from the database
$db = null;
require_once __DIR__ . '/includes/bootstrap.php';

// Fetch all products before rendering the page
$pageTitle = 'Products | ' . SITE_NAME;
$products = fetch_products($db);
$selectedStock = "all";
$validStockFilters = ["all", "in-stock", "last-few", "out-of-stock"];

// Read the requested stock filter from the query string
if (isset($_GET["stock"])) {
    $requestedStock = strtolower(trim($_GET["stock"]));

    if (in_array($requestedStock, $validStockFilters, true)) {
        $selectedStock = $requestedStock;
    }
}

require __DIR__ . '/includes/header.php';

// Show the product catalogue with stock status and add-to-cart controls
echo "<section>";
echo "<h2>Products</h2>";

if (!$db) {
    echo "<p class='status-message error'>Database is not connected yet. Update credentials in <code>includes/bootstrap.php</code>.</p>";
} elseif ($products === []) {
    echo "<p class='status-message'>No products found in <code>tbl_products</code>.</p>";
} else {
    $filteredProducts = [];

    // Keep only the products that match the selected stock filter
    foreach ($products as $product) {
        $statusClass = product_status_class($product["status"]);

        if ($selectedStock !== "all" && $statusClass !== $selectedStock) {
            continue;
        }

        $filteredProducts[] = $product;
    }

    // Show the stock filter above the product grid
    echo "<form class='product-filter-form' method='get' action='products.php'>";
    echo "<label for='stock-filter'>Filter by stock:</label>";
    echo "<select id='stock-filter' name='stock' onchange='this.form.submit()'>";
    echo "<option value='all'" . ($selectedStock === "all" ? " selected" : "") . ">All</option>";
    echo "<option value='in-stock'" . ($selectedStock === "in-stock" ? " selected" : "") . ">Good Stock</option>";
    echo "<option value='last-few'" . ($selectedStock === "last-few" ? " selected" : "") . ">Last Few</option>";
    echo "<option value='out-of-stock'" . ($selectedStock === "out-of-stock" ? " selected" : "") . ">Out of Stock</option>";
    echo "</select>";
    echo "<noscript><button type='submit'>Apply</button></noscript>";
    echo "</form>";

    if ($filteredProducts === []) {
        echo "<p class='status-message'>No products match the selected stock filter.</p>";
        echo "</section>";
        require __DIR__ . '/includes/footer.php';
        return;
    }

    echo "<div class='product-container'>";

    // Build one card per product using the shared stock-status CSS classes
    foreach ($filteredProducts as $product) {
        $statusClass = product_status_class($product["status"]);
        $quantityInputId = "quantity_" . $product["id"];
        $buttonText = "Add to Cart";
        $disabled = "";

        if ($statusClass === "out-of-stock") {
            $buttonText = "Out of Stock";
            $disabled = " disabled";
        }

        echo "<div class='product-card " . e($statusClass) . "'>";
        echo "<a href='item.php?id=" . urlencode($product["id"]) . "'>";
        echo "<img src='" . e($product["image"]) . "' alt='" . e($product["name"]) . "' class='product-image'>";
        echo "</a>";
        echo "<h3>" . e($product["name"]) . "</h3>";
        echo "<p>" . e($product["description"]) . "</p>";
        echo "<p>Price: &pound;" . e(format_price($product["price"])) . "</p>";
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
        echo "</div>";
    }

    echo "</div>";
}

echo "</section>";

require __DIR__ . '/includes/footer.php';

