<?php class OrderController
{
    private PDO $conn;
    private Product $productModel;
    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
        $this->productModel = new Product($conn);
    }
    public function add(int $id)
    {
        $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
    }
    public function increase(int $id)
    {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]++;
        }
    }
    public function decrease(int $id)
    {
        if (!isset($_SESSION['cart'][$id])) {
            return;
        }
        if ($_SESSION['cart'][$id] > 1) {
            $_SESSION['cart'][$id]--;
        } else {
            unset($_SESSION['cart'][$id]);
        }
    }
    public function confirm(?int $roomId, string $notes)
    {
        if (empty($_SESSION['cart'])) {
            return null;
        }
        $this->conn->beginTransaction();
        try {
            $totalPrice = 0;
            foreach ($_SESSION['cart'] as $productId => $qty) {
                $product = $this->productModel->getById($productId);
                if (!$product) {
                    continue;
                }
                $totalPrice += $product['price'] * $qty;
            }
            $orderStmt = $this->conn->prepare(" INSERT INTO orders (room_id, notes, total_price, created_at) VALUES (?, ?, ?, NOW()) ");
            $orderStmt->execute([$roomId, $notes, $totalPrice]);
            $orderId = $this->conn->lastInsertId();
            $itemStmt = $this->conn->prepare(" INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?) ");
            foreach ($_SESSION['cart'] as $productId => $qty) {
                $product = $this->productModel->getById($productId);
                if ($product) {
                    $itemStmt->execute([$orderId, $productId, $qty, $product['price']]);
                }
            }
            $this->conn->commit();
            $_SESSION['cart'] = [];
            return $orderId;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }
    public function getLatestOrder()
    {
        $stmt = $this->conn->query(" SELECT * FROM orders_with_rooms ORDER BY created_at DESC LIMIT 1 ");
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$order) {
            return null;
        }
        $itemsStmt = $this->conn->prepare(" SELECT * FROM order_items_with_products WHERE order_id = ? ");
        $itemsStmt->execute([$order['id']]);
        return ['order' => $order, 'items' => $itemsStmt->fetchAll(PDO::FETCH_ASSOC)];
    }
}
