<?php

define('DB_HOST', 'localhost');
define('DB_PORT', '3307'); // add ur port 
define('DB_NAME', 'cafeteria_db');
define('DB_USER', 'root');
define('DB_PASS', '');

define('BASE_URL',  'http://localhost/PHP-CAFETERA/public');
define('PRODUCTS_PER_PAGE', 5); // PAGINATION

// define('UPLOAD_PATH', dirname(__DIR__, 2), 'public/assets/uploads/products/' );
define('UPLOAD_URL', BASE_URL . '/assets/uploads/products/');

define('DEBUG_MODE', true); // for debugging 
if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}