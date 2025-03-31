<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'bookswap');

// Site configuration
define('SITE_NAME', 'BookSwap');
define('SITE_URL', 'http://localhost/bookswap');
define('MAX_DISTANCE', 50); // Maximum distance in miles to show nearby books

// Start session
session_start();

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>