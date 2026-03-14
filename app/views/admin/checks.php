<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../controllers/OrderController.php';

$database = new Database();
$db = $database->connect();
$orderController = new OrderController($db);

$fromDate = trim($_GET['from'] ?? '');
$toDate = trim($_GET['to'] ?? '');
$selectedUserId = (int)($_GET['user_id'] ?? 0);

$summary = $orderController->getUserChecksSummary($fromDate !== '' ? $fromDate : null, $toDate !== '' ? $toDate : null);

$selectedUser = null;
foreach ($summary as $row) {
	if ((int)$row['user_id'] === $selectedUserId) {
		$selectedUser = $row;
		break;
	}
}

$selectedUserOrders = [];
$selectedUserItems = [];

if ($selectedUserId > 0) {
	$selectedUserOrders = $orderController->getUserOrdersForChecks($selectedUserId, $fromDate !== '' ? $fromDate : null, $toDate !== '' ? $toDate : null);
	$orderIds = array_map(static fn($order) => (int)$order['id'], $selectedUserOrders);
	$selectedUserItems = $orderController->getItemsForOrders($orderIds);
}

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
	<title>Admin Checks</title>
	<?php include __DIR__ . '/../layouts/jsCDN.php'; ?>
	<style>
		body {
			background: #f4f6f9;
		}

		.card {
			border: none;
			box-shadow: 0 5px 15px rgba(0, 0, 0, .08);
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
		<div class="card mb-4">
			<div class="card-header bg-white">
				<h4 class="mb-0">Checks</h4>
				<small class="text-muted">Users total spending with order drill-down</small>
			</div>

			<div class="card-body border-bottom">
				<form class="row g-3" method="GET" action="/admin/checks">
					<div class="col-md-4">
						<label class="form-label">From Date</label>
						<input type="date" name="from" class="form-control" value="<?= htmlspecialchars($fromDate) ?>">
					</div>
					<div class="col-md-4">
						<label class="form-label">To Date</label>
						<input type="date" name="to" class="form-control" value="<?= htmlspecialchars($toDate) ?>">
					</div>
					<?php if ($selectedUserId > 0): ?>
						<input type="hidden" name="user_id" value="<?= $selectedUserId ?>">
					<?php endif; ?>
					<div class="col-md-4 d-flex align-items-end gap-2">
						<button type="submit" class="btn btn-primary">Filter</button>
						<a href="/admin/checks" class="btn btn-outline-secondary">Clear</a>
					</div>
				</form>
			</div>

			<div class="card-body">
				<?php if (empty($summary)): ?>
					<div class="alert alert-info mb-0">No user checks found for the selected range.</div>
				<?php else: ?>
					<div class="table-responsive">
						<table class="table table-hover align-middle mb-0">
							<thead class="table-light">
								<tr>
									<th>User</th>
									<th>Email</th>
									<th>Orders</th>
									<th>Total Spent</th>
									<th>Details</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($summary as $user): ?>
									<?php
									$userName = $user['user_name'] ?? 'Unknown';
									$userFallback = 'https://ui-avatars.com/api/?name=' . rawurlencode((string)$userName);
									$userImageSrc = $resolveUserImage($user['user_image'] ?? null, (string)$userName);

									$params = [
										'user_id' => (int)$user['user_id'],
									];
									if ($fromDate !== '') {
										$params['from'] = $fromDate;
									}
									if ($toDate !== '') {
										$params['to'] = $toDate;
									}
									$detailsUrl = '/admin/checks?' . http_build_query($params);
									?>
									<tr>
										<td>
											<div class="d-flex align-items-center gap-2">
												<img src="<?= htmlspecialchars($userImageSrc) ?>"
													alt="<?= htmlspecialchars((string)$userName) ?>"
													class="user-avatar"
													onerror="this.onerror=null;this.src='<?= htmlspecialchars($userFallback) ?>';">
												<span class="fw-semibold"><?= htmlspecialchars((string)$userName) ?></span>
											</div>
										</td>
										<td><?= htmlspecialchars($user['user_email']) ?></td>
										<td><?= (int)$user['orders_count'] ?></td>
										<td><?= number_format((float)$user['total_spent'], 2) ?></td>
										<td>
											<a href="<?= htmlspecialchars($detailsUrl) ?>" class="btn btn-sm btn-outline-primary">
												View Orders
											</a>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<?php if ($selectedUserId > 0): ?>
			<div class="card">
				<div class="card-header bg-white d-flex justify-content-between align-items-center">
					<div>
						<?php
						$selectedUserName = $selectedUser['user_name'] ?? ('User #' . $selectedUserId);
						$selectedUserImageSrc = $resolveUserImage($selectedUser['user_image'] ?? null, (string)$selectedUserName);
						?>
						<div class="d-flex align-items-center gap-2 mb-1">
							<img src="<?= htmlspecialchars($selectedUserImageSrc) ?>" alt="<?= htmlspecialchars((string)$selectedUserName) ?>" class="user-avatar">
							<h5 class="mb-0"><?= htmlspecialchars((string)$selectedUserName) ?> Orders</h5>
						</div>
						<?php if (!empty($selectedUser['user_email'])): ?>
							<small class="text-muted"><?= htmlspecialchars($selectedUser['user_email']) ?></small>
						<?php endif; ?>
					</div>
					<span class="badge bg-dark">Total Orders: <?= count($selectedUserOrders) ?></span>
				</div>

				<div class="card-body">
					<?php if (empty($selectedUserOrders)): ?>
						<div class="alert alert-warning mb-0">This user has no orders in the selected date range.</div>
					<?php else: ?>
						<div class="table-responsive">
							<table class="table table-hover align-middle mb-0">
								<thead class="table-light">
									<tr>
										<th>Order</th>
										<th>Room</th>
										<th>Total</th>
										<th>Status</th>
										<th>Date</th>
										<th>Details</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($selectedUserOrders as $order): ?>
										<?php
										$orderId = (int)$order['id'];
										$status = $order['status'] ?? 'processing';
										$statusMeta = $statusLabels[$status] ?? ['class' => 'secondary', 'label' => ucfirst((string)$status)];
										$detailsId = 'check-order-details-' . $orderId;
										$orderItems = $selectedUserItems[$orderId] ?? [];
										?>
										<tr>
											<td>#<?= $orderId ?></td>
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
											<td colspan="6" class="bg-light p-0 border-0">
												<div class="collapse" id="<?= $detailsId ?>">
													<div class="p-3">
														<?php if (empty($orderItems)): ?>
															<div class="text-muted py-2">No order items found.</div>
														<?php else: ?>
															<div class="table-responsive">
																<table class="table table-sm mb-0">
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
		<?php endif; ?>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
		crossorigin="anonymous"></script>
</body>

</html>
