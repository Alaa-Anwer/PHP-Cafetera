<?php

// Load database configuration
require_once __DIR__ . "/../app/config/Database.php";
require_once __DIR__ . '/../app/controllers/HomeController.php';
require_once __DIR__ . '/../app/controllers/OrderController.php';

// Initialize database connection
$database = new Database();
$db = $database->connect();

// Get request URI
$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// Route to controllers
switch ($uri) {

    case "/":
        $controller = new HomeController();
        $controller->index();
        break;

    case "/login":
        require_once __DIR__ . "/../app/controllers/AuthController.php";
        $controller = new AuthController();
        $controller->login();
        break;

    case "/forgot-password":
        require_once __DIR__ . "/../app/controllers/AuthController.php";
        $controller = new AuthController();
        $controller->forgotPassword();
        break;

    case "/reset-password":
        require_once __DIR__ . "/../app/controllers/AuthController.php";
        $controller = new AuthController();
        $controller->resetPassword();
        break;

    case "/logout":
        require_once __DIR__ . "/../app/controllers/AuthController.php";
        $controller = new AuthController();
        $controller->logout();
        break;

    case "/admin/users":
        require_once __DIR__ . "/../app/controllers/UserController.php";
        $controller = new UserController($db);
        $controller->index();
        break;

    case "/admin/add-user":
        require_once __DIR__ . "/../app/controllers/UserController.php";
        $controller = new UserController($db);
        $controller->create();
        break;

    case "/home":
        $controller = new HomeController();
        $controller->index();
        break;

    case "/cart/add":
        $controller = new OrderController($db);
        $controller->add((int)$_GET['id']);
        header("Location: /");
        exit();
        break;

    case "/cart/plus":
        $controller = new OrderController($db);
        $controller->increase((int)$_GET['id']);
        header("Location: /");
        exit();
        break;

    case "/cart/minus":
        $controller = new OrderController($db);
        $controller->decrease((int)$_GET['id']);
        header("Location: /");
        exit();
        break;

    case "/order/confirm":
        $controller = new OrderController($db);
        $controller->confirm($_POST['room_id'] ?? null, $_POST['notes'] ?? '');
        header("Location: /");
        exit();
        break;

    case "/order/latest":
        $controller = new OrderController($db);
        $controller->getLatestOrder();
        break;


    default:
        // 404 Not Found
        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1>";
        echo "<p>Requested: " . htmlspecialchars($uri) . "</p>";
        echo "<p><a href='/'>Go Home</a> | <a href='/login'>Login</a></p>";
        break;
}
?>
