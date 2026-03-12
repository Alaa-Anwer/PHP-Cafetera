<?php require_once __DIR__ . '/../app/config/Database.php';
require_once __DIR__ . '/../app/controllers/HomeController.php';
require_once __DIR__ . '/../app/controllers/OrderController.php';
function handle_cart_action(OrderController $orderController): void
{
    if (empty($_GET['action']) || empty($_GET['id'])) {
        return;
    }
    $action = $_GET['action'];
    $id = (int) $_GET['id'];
    switch ($action) {
        case 'add':
            $orderController->add($id);
            break;
        case 'plus':
            $orderController->increase($id);
            break;
        case 'minus':
            $orderController->decrease($id);
            break;
        default:
            return;
    }
    header("Location: index.php");
    exit();
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$action = $_GET['action'] ?? 'home';
switch ($action) {
    case 'add':
    case 'plus':
    case 'minus':
        $db = new Database();
        $conn = $db->connect();
        $orderController = new OrderController($conn);
        handle_cart_action($orderController);
        break;
    case 'home':
    default:
        $home = new HomeController();
        $home->index();
        break;
}
