<?php
// Close the shared layout and load the JavaScript used by the shop pages
echo "</main>";
echo "<footer>";
echo "<button id='top-of-page' type='button' onclick='window.scrollTo({top: 0, behavior: \"smooth\"});'>&#8593;</button>";
echo "<p>&copy; " . date('Y') . " UClan Web Page. All rights reserved.</p>";
echo "</footer>";
echo "<script src='cart.js'></script>";
echo "</body>";
echo "</html>";

