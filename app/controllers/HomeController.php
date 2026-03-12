<?php require_once __DIR__ . "/../models/Product.php";
require_once __DIR__ . "/../models/Room.php";
require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/OrderController.php";
require_once __DIR__ . "/../../routes/web.php";
class HomeController
{
    public function index()
    {
        $db = new Database();
        $conn = $db->connect();
        $productModel = new Product($conn);
        $roomModel = new Room($conn);
        $orderController = new OrderController($conn);
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        if (isset($_GET['action'])) {
            handle_cart_action($orderController);
        }
        if (isset($_GET['add'])) {
            $orderController->add((int) $_GET['add']);
            header("Location: index.php");
            exit();
        }
        if (isset($_GET['plus'])) {
            $orderController->increase((int) $_GET['plus']);
            header("Location: index.php");
            exit();
        }
        if (isset($_GET['minus'])) {
            $orderController->decrease((int) $_GET['minus']);
            header("Location: index.php");
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_order'])) {
            $roomId = !empty($_POST['room_id']) ? (int) $_POST['room_id'] : null;
            $notes = trim($_POST['notes'] ?? '');
            $orderController->confirm($roomId, $notes);
            header("Location: index.php");
            exit();
        }
        $products = $productModel->getAll();
        $rooms = $roomModel->getAll();
        $cartItems = [];
        $totalPrice = 0;
        foreach ($_SESSION['cart'] as $productId => $quantity) {
            $product = $productModel->getById($productId);
            if (!$product) {
                continue;
            }
            $lineTotal = $product['price'] * $quantity;
            $totalPrice += $lineTotal;
            $cartItems[] = ['product' => $product, 'quantity' => $quantity, 'line_total' => $lineTotal];
        }
        $latestOrder = $orderController->getLatestOrder();
        require __DIR__ . "/../views/user/home.php";
    }
}
