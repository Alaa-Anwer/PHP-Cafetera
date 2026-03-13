<link rel="stylesheet" href="/PHP-Cafetera/public/assets/css/bootstrap.css">
<link rel="stylesheet" href="/PHP-Cafetera/public/assets/css/sidebar.css">

<style>
  .panel {
    background: #f7f1ea;
    border-radius: 16px;
    border: 1px solid rgba(0,0,0,.08);
    padding: 22px;
  }
  .product-img {
    width: 55px;
    height: 55px;
    object-fit: cover;
    border-radius: 8px;
  }
  .badge-category {
    background: #e8f5e9;
    color: #2e7d32;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: .8rem;
    font-weight: 600;
  }
</style>

<div class="app">
  <?php
    $active = 'products';
    require_once __DIR__ . '/../layouts/sidebar.php';
    require_once __DIR__ . '/../../controllers/productController.php';

    $productController = new ProductController();
    $products          = $productController->getAllProducts();
  ?>

  <main class="content">
    <div class="panel">

      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h2 class="mb-0">All Products</h2>
          <div class="text-muted small">Dashboard &gt; Products</div>
        </div>
        <a href="/PHP-CAFETERA/app/views/admin/add_product.php" 
           class="btn btn-success">
          + Add New Product
        </a>
      </div>

      <?php if(isset($_GET['msg'])): ?>
        <?php switch($_GET['msg']):
          case 'product_updated': ?>
            <div class="alert alert-success alert-dismissible fade show">
              Product Updated Successfully!
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php break; ?>

          <?php case 'product_deleted': ?>
            <div class="alert alert-warning alert-dismissible fade show">
               Product Deleted Successfully!
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php break; ?>

          <?php case 'error': ?>
            <div class="alert alert-danger alert-dismissible fade show">
              Something went wrong!
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php break; ?>

        <?php endswitch; ?>
      <?php endif; ?>

      <!-- Table -->
      <?php if(empty($products)): ?>
        <div class="text-center text-muted py-5">
          <h5>No products found</h5>
          <a href="/PHP-CAFETERA/app/views/admin/add_product.php" class="btn btn-success mt-2">
            Add Your First Product
          </a>
        </div>

      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Created At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($products as $index => $product): ?>
                <tr>
                  <td><?= $index + 1 ?></td>

                  <td>
                    <img 
                      src="/PHP-CAFETERA/public/assets/images/products/<?= htmlspecialchars($product['image']) ?>" 
                      alt="<?= htmlspecialchars($product['name']) ?>"
                      class="product-img"
                    >
                  </td>

                  <td><?= htmlspecialchars($product['name']) ?></td>

                  <td>
                    <span class="badge-category">
                      <?= htmlspecialchars($product['category_name']) ?>
                    </span>
                  </td>

                  <td>EGP <?= number_format($product['price'], 2) ?></td>

                  <td><?= date('d M Y', strtotime($product['created_at'])) ?></td>

                  <td>
                    <div class="d-flex gap-2">
                      <a href="/PHP-CAFETERA/app/views/admin/edit_product.php?id=<?= $product['id'] ?>" 
                         class="btn btn-sm btn-outline-primary">
                        Edit
                      </a>

                      <a href="/PHP-CAFETERA/app/controllers/productController.php?delete_product=1&id=<?= $product['id'] ?>&image=<?= urlencode($product['image']) ?>"
                         class="btn btn-sm btn-outline-danger"
                         onclick="return confirm('Are you sure you want to delete <?= htmlspecialchars($product['name']) ?>?')">
                         Delete
                      </a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

    </div>
  </main>
</div>