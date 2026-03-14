<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../controllers/OrderController.php';

$database = new Database();
$db = $database->connect();
$orderController = new OrderController($db);

$fromDate = trim($_GET['from'] ?? '');
$toDate = trim($_GET['to'] ?? '');

$orders = $orderController->getAllForAdmin($fromDate !== '' ? $fromDate : null, $toDate !== '' ? $toDate : null);
$orderIds = array_map(static fn($order) => (int)$order['id'], $orders);
$itemsByOrder = $orderController->getItemsForOrders($orderIds);

$resolveUserImage = static function (?string $image, string $name): string {
	$image = trim((string)$image);
	if ($image !== '' && filter_var($image, FILTER_VALIDATE_URL)) {
		return $image;
	}

	if ($image !== '') {
		return '/uploads/' . rawurlencode($image);
	}

	return 'https://ui-avatars.com/api/?name=' . rawurlencode($name);
};

$resolveProductImage = static function (?string $image): string {
	if (empty($image)) {
		return '';
	}

	if (filter_var($image, FILTER_VALIDATE_URL)) {
		return $image;
	}

	$productImageFs = __DIR__ . '/../../../public/assets/images/products/' . $image;
	$legacyImageFs = __DIR__ . '/../../../public/assets/images/' . $image;

	if (file_exists($productImageFs)) {
		return '/assets/images/products/' . rawurlencode($image);
	}

	if (file_exists($legacyImageFs)) {
		return '/assets/images/' . rawurlencode($image);
	}

	return '';
};

$statusLabels = [
	'processing' => ['class' => 'warning', 'label' => 'Processing'],
	'out_for_delivery' => ['class' => 'info', 'label' => 'Out For Delivery'],
	'done' => ['class' => 'success', 'label' => 'Done'],
	'cancelled' => ['class' => 'danger', 'label' => 'Cancelled'],
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Admin Orders</title>
	<?php include __DIR__ . '/../layouts/jsCDN.php'; ?>
	<style>
		body {
			background: #f4f6f9;
		}

		.card {
			border: none;
			box-shadow: 0 5px 15px rgba(0, 0, 0, .08);
		}

		.order-items-table td,
		.order-items-table th {
			font-size: .92rem;
		}

		.user-avatar,
		.product-thumb {
			width: 40px;
			height: 40px;
			object-fit: cover;
		}

		.user-avatar {
			border-radius: 50%;
		}

		.product-thumb {
			border-radius: 8px;
		}
	</style>
</head>

<body>
	<?php include __DIR__ . '/../layouts/navbar.php'; ?>

	<div class="container py-4">
		<div class="card">
			<div class="card-header bg-white d-flex justify-content-between align-items-center">
				<div>
					<h4 class="mb-0">Orders</h4>
					<small class="text-muted">All users orders with order details</small>
				</div>
			</div>

			<div class="card-body border-bottom">
				<form class="row g-3" method="GET" action="/admin/orders">
					<div class="col-md-4">
						<label class="form-label">From Date</label>
						<input type="date" name="from" class="form-control" value="<?= htmlspecialchars($fromDate) ?>">
					</div>
					<div class="col-md-4">
						<label class="form-label">To Date</label>
						<input type="date" name="to" class="form-control" value="<?= htmlspecialchars($toDate) ?>">
					</div>
					<div class="col-md-4 d-flex align-items-end gap-2">
						<button type="submit" class="btn btn-primary">Filter</button>
						<a href="/admin/orders" class="btn btn-outline-secondary">Clear</a>
					</div>
				</form>
			</div>

			<div class="card-body">
				<?php if (empty($orders)): ?>
					<div class="alert alert-info mb-0">No orders found for the selected range.</div>
				<?php else: ?>
					<div class="table-responsive">
						<table class="table table-hover align-middle mb-0">
							<thead class="table-light">
								<tr>
									<th>Order</th>
									<th>User</th>
									<th>Room</th>
									<th>Total</th>
									<th>Status</th>
									<th>Date</th>
									<th>Details</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($orders as $order): ?>
									<?php
									$orderId = (int)$order['id'];
									$status = $order['status'] ?? 'processing';
									$statusMeta = $statusLabels[$status] ?? ['class' => 'secondary', 'label' => ucfirst((string)$status)];
									$detailsId = 'order-details-' . $orderId;
									$orderItems = $itemsByOrder[$orderId] ?? [];
									?>
									<tr>
										<td>#<?= $orderId ?></td>
										<td>
											<?php
											$userName = $order['user_name'] ?? 'Unknown';
											$userEmail = $order['user_email'] ?? '';
											$userImageSrc = $resolveUserImage($order['user_image'] ?? null, (string)$userName);
											?>
											<div class="d-flex align-items-center gap-2">
												<img src="<?= htmlspecialchars($userImageSrc) ?>" alt="<?= htmlspecialchars((string)$userName) ?>" class="user-avatar">
												<div>
													<div class="fw-semibold"><?= htmlspecialchars((string)$userName) ?></div>
													<small class="text-muted"><?= htmlspecialchars((string)$userEmail) ?></small>
												</div>
											</div>
										</td>
										<td><?= htmlspecialchars($order['room_name'] ?? 'N/A') ?></td>
										<td><?= number_format((float)$order['total_price'], 2) ?></td>
										<td><span class="badge bg-<?= $statusMeta['class'] ?>"><?= $statusMeta['label'] ?></span></td>
										<td><?= date('M d, Y H:i', strtotime($order['created_at'])) ?></td>
										<td>
											<button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $detailsId ?>">
												View Items
											</button>
										</td>
									</tr>

									<tr>
										<td colspan="7" class="bg-light p-0 border-0">
											<div class="collapse" id="<?= $detailsId ?>">
												<div class="p-3">
													<?php if (empty($orderItems)): ?>
														<div class="text-muted py-2">No order items found.</div>
													<?php else: ?>
														<div class="table-responsive">
															<table class="table table-sm order-items-table mb-0">
																<thead>
																	<tr>
																		<th>Product</th>
																		<th>Price</th>
																		<th>Qty</th>
																		<th>Line Total</th>
																	</tr>
																</thead>
																<tbody>
																	<?php foreach ($orderItems as $item): ?>
																			<?php
																			$productName = $item['product_name'] ?? 'Unknown Product';
																			$productImageSrc = $resolveProductImage($item['product_image'] ?? null);
																			?>
																		<tr>
																				<td>
																					<div class="d-flex align-items-center gap-2">
																						<?php if ($productImageSrc !== ''): ?>
																							<img src="<?= htmlspecialchars($productImageSrc) ?>" alt="<?= htmlspecialchars((string)$productName) ?>" class="product-thumb">
																						<?php endif; ?>
																						<span><?= htmlspecialchars((string)$productName) ?></span>
																					</div>
																				</td>
																			<td><?= number_format((float)$item['price'], 2) ?></td>
																			<td><?= (int)$item['quantity'] ?></td>
																			<td><?= number_format((float)$item['price'] * (int)$item['quantity'], 2) ?></td>
																		</tr>
																	<?php endforeach; ?>
																</tbody>
															</table>
														</div>
														<?php if (!empty($order['notes'])): ?>
															<div class="mt-2">
																<strong>Notes:</strong>
																<span class="text-muted"><?= nl2br(htmlspecialchars($order['notes'])) ?></span>
															</div>
														<?php endif; ?>
													<?php endif; ?>
												</div>
											</div>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
		crossorigin="anonymous"></script>
</body>

</html>
