<?php

$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Remove query string from URI
$request = parse_url($request, PHP_URL_PATH);

// Database connection
require_once __DIR__ . '/../app/config/Database.php';
$database = new Database();
$db = $database->connect();

// Controllers
require_once __DIR__ . '/../app/controllers/UserController.php';

/// Route definitions
// Users routes
if (preg_match('/\/users$/', $request) && $method === 'GET') {
    $controller = new UserController($db);
    $controller->index();
}
elseif (preg_match('/\/users\/create$/', $request) && $method === 'GET') {
    $controller = new UserController($db);
    $controller->create(); // Redirect to create form
}
elseif (preg_match('/\/users\/(\d+)\/delete$/', $request, $matches) && $method === 'POST') {
    $id = $matches[1];
    $controller = new UserController($db);
    $controller->delete($id);
    header('Location: /users');
}
else {
    require_once __DIR__ . '/../app/views/user/home.php';
}
