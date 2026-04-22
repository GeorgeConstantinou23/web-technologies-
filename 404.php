<?php

// Responsible for showing a custom page when a requested page cannot be found
http_response_code(404);

$db = null;
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Page Not Found | ' . SITE_NAME;

require __DIR__ . '/includes/header.php';

echo "<section class='error-page'>";
echo "<h1>404</h1>";
echo "<h2>Page Not Found</h2>";
echo "<p>Sorry, the page you are looking for does not exist.</p>";
echo "<p>Please check the URL or return to the homepage.</p>";
echo "<a class='back-link' href='index.php'>Back to homepage</a>";
echo "</section>";

require __DIR__ . '/includes/footer.php';
