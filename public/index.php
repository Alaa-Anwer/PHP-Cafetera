<?php

// Start session
session_start();

// Basic headers
header("Content-Type: text/html; charset=UTF-8");

// Autoload / Router
require_once __DIR__ . '/../routes/web.php';

?>