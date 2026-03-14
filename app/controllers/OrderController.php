<?php

class OrderController
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

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
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

            $orderStmt = $this->conn->prepare("
                INSERT INTO orders (user_id, room_id, notes, total_price, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ");

            $orderStmt->execute([
                $userId,
                $roomId,
                $notes,
                $totalPrice
            ]);

            $orderId = $this->conn->lastInsertId();

            $itemStmt = $this->conn->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price)
                VALUES (?, ?, ?, ?)
            ");

            foreach ($_SESSION['cart'] as $productId => $qty) {

                $product = $this->productModel->getById($productId);

                if ($product) {
                    $itemStmt->execute([
                        $orderId,
                        $productId,
                        $qty,
                        $product['price']
                    ]);
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
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            return null;
        }

        $stmt = $this->conn->prepare("
            SELECT o.*, r.name as room_name
            FROM orders o
            LEFT JOIN rooms r ON o.room_id = r.id
            WHERE o.user_id = ?
            ORDER BY o.created_at DESC
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            return null;
        }

        $itemsStmt = $this->conn->prepare("
            SELECT oi.*, p.name as product_name, p.image as product_image
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");

        $itemsStmt->execute([$order['id']]);

        return [
            'order' => $order,
            'items' => $itemsStmt->fetchAll(PDO::FETCH_ASSOC)
        ];
    }

    public function getByUserId(int $userId)
    {
        $stmt = $this->conn->prepare("
            SELECT o.*, r.name as room_name
            FROM orders o
            LEFT JOIN rooms r ON o.room_id = r.id
            WHERE o.user_id = ?
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllForAdmin(?string $fromDate = null, ?string $toDate = null)
    {
        $conditions = [];
        $params = [];

        if (!empty($fromDate)) {
            $conditions[] = "DATE(o.created_at) >= ?";
            $params[] = $fromDate;
        }

        if (!empty($toDate)) {
            $conditions[] = "DATE(o.created_at) <= ?";
            $params[] = $toDate;
        }

        $whereSql = empty($conditions) ? '' : ('WHERE ' . implode(' AND ', $conditions));

        $stmt = $this->conn->prepare("
            SELECT o.*, u.name as user_name, u.email as user_email, u.image as user_image, r.name as room_name
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN rooms r ON o.room_id = r.id
            {$whereSql}
            ORDER BY o.created_at DESC
        ");

        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getItemsForOrders(array $orderIds)
    {
        if (empty($orderIds)) {
            return [];
        }

        $orderIds = array_values(array_unique(array_map('intval', $orderIds)));
        $placeholders = implode(',', array_fill(0, count($orderIds), '?'));

        $stmt = $this->conn->prepare("
            SELECT oi.*, p.name as product_name, p.image as product_image
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id IN ({$placeholders})
            ORDER BY oi.order_id ASC, oi.id ASC
        ");
        $stmt->execute($orderIds);

        $grouped = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $item) {
            $orderId = (int)($item['order_id'] ?? 0);
            if (!isset($grouped[$orderId])) {
                $grouped[$orderId] = [];
            }
            $grouped[$orderId][] = $item;
        }

        return $grouped;
    }

    public function getUserChecksSummary(?string $fromDate = null, ?string $toDate = null)
    {
        $conditions = [];
        $params = [];

        if (!empty($fromDate)) {
            $conditions[] = "DATE(o.created_at) >= ?";
            $params[] = $fromDate;
        }

        if (!empty($toDate)) {
            $conditions[] = "DATE(o.created_at) <= ?";
            $params[] = $toDate;
        }

        $whereSql = empty($conditions) ? '' : ('WHERE ' . implode(' AND ', $conditions));

        $stmt = $this->conn->prepare("
            SELECT
                u.id as user_id,
                u.name as user_name,
                u.email as user_email,
                u.image as user_image,
                COUNT(o.id) as orders_count,
                COALESCE(SUM(o.total_price), 0) as total_spent
            FROM users u
            LEFT JOIN orders o ON o.user_id = u.id
            {$whereSql}
            GROUP BY u.id, u.name, u.email, u.image
            HAVING COUNT(o.id) > 0
            ORDER BY total_spent DESC, user_name ASC
        ");

        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserOrdersForChecks(int $userId, ?string $fromDate = null, ?string $toDate = null)
    {
        $conditions = ["o.user_id = ?"];
        $params = [$userId];

        if (!empty($fromDate)) {
            $conditions[] = "DATE(o.created_at) >= ?";
            $params[] = $fromDate;
        }

        if (!empty($toDate)) {
            $conditions[] = "DATE(o.created_at) <= ?";
            $params[] = $toDate;
        }

        $whereSql = 'WHERE ' . implode(' AND ', $conditions);

        $stmt = $this->conn->prepare("
            SELECT o.*, r.name as room_name
            FROM orders o
            LEFT JOIN rooms r ON o.room_id = r.id
            {$whereSql}
            ORDER BY o.created_at DESC
        ");
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $orderId)
    {
        $stmt = $this->conn->prepare("
            SELECT o.*, r.name as room_name, u.name as user_name
            FROM orders o
            LEFT JOIN rooms r ON o.room_id = r.id
            LEFT JOIN users u ON o.user_id = u.id
            WHERE o.id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getItems(int $orderId)
    {
        $stmt = $this->conn->prepare("
            SELECT oi.*, p.name as product_name, p.image as product_image
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cancel(int $orderId, int $userId)
    {
        $stmt = $this->conn->prepare("
            UPDATE orders 
            SET status = 'cancelled' 
            WHERE id = ? AND user_id = ? AND status = 'processing'
        ");
            $stmt->execute([$orderId, $userId]);
            return $stmt->rowCount() > 0;
    }
}
