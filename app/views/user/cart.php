<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cart</title>
    <link rel="stylesheet" href="assets/css/bootstrap.css">
</head>

<body>
    <div class="container py-4">
        <h2 class="mb-3">Your Cart</h2>
        <div class="card mb-4">
            <div class="card-header">
                Cart Items
            </div>
            <div class="card-body">
                <?php if (!empty($cartItems)): ?>
                    <ul class="list-group mb-3">
                        <?php foreach ($cartItems as $item): ?>
                            <?php $product = $item['product']; ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold"><?= htmlspecialchars($product['name']) ?></div>
                                    <div class="small text-muted">
                                        <?= number_format((float) $product['price'], 2) ?> each
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <a href="?action=minus&id=<?= (int) $product['id'] ?>" class="btn btn-sm btn-outline-secondary me-1">-</a>
                                    <span class="mx-1"><?= (int) $item['quantity'] ?></span>
                                    <a href="?action=plus&id=<?= (int) $product['id'] ?>" class="btn btn-sm btn-outline-secondary ms-1">+</a>
                                </div>
                                <div class="ms-3 text-end">
                                    <small class="text-muted">Total</small>
                                    <div><?= number_format((float) $item['line_total'], 2) ?></div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="d-flex justify-content-between">
                        <strong>Total Price</strong>
                        <strong><?= number_format((float) $totalPrice, 2) ?></strong>
                    </div>
                <?php else: ?>
                    <p class="mb-0 text-muted">Cart is empty.</p>
                <?php endif; ?>
            </div>
        </div>

        <a href="index.php" class="btn btn-primary">Back to Home</a>
    </div>

    <script src="assets/js/jquery-3.7.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.js"></script>
</body>

</html>