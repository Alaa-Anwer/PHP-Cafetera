<link rel="stylesheet" href="/PHP-Cafetera/public/assets/css/bootstrap.css">
<link rel="stylesheet" href="/PHP-Cafetera/public/assets/css/sidebar.css">

<style>
  .panel {
    background: #f7f1ea;
    border-radius: 16px;
    border: 1px solid rgba(0,0,0,.08);
    padding: 22px;
  }
  .req { color: #b42318; font-weight: 600; }
  .upload-box {
    border: 2px dashed rgba(0,0,0,.25);
    border-radius: 14px;
    background: rgba(255,255,255,.35);
    min-height: 240px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 18px;
  }
  .upload-box input[type="file"] { display: none; }
  .upload-label { cursor: pointer; width: 100%; }
  #preview {
    max-width: 100%;
    max-height: 220px;
    border-radius: 10px;
    margin: 0 auto;
  }
</style>

<div class="app">
  <?php
    $active = 'products';
    require_once __DIR__ . '/../layouts/sidebar.php';
    require_once __DIR__ . '/../../controllers/productController.php';
    require_once __DIR__ . '/../../controllers/categoryController.php';

    $productController = new ProductController();

    $id = (int)($_GET['id'] ?? 0);

    if (!isset($product)) {
        $product = $productController->getProductById($id);
    }

    if (!$product) {
        echo '<div class="alert alert-danger m-4">Product not found!</div>';
        exit();
    }
  ?>

  <main class="content">
    <div class="panel">

      <div class="d-flex justify-content-between align-items-center mb-1">
        <h2 class="mb-0">Edit Product</h2>
        <a href="/PHP-CAFETERA/app/views/admin/products.php" 
           class="btn btn-outline-secondary btn-sm">
          ← Back to Products
        </a>
      </div>
      <div class="text-muted small mb-4">
        Dashboard &gt; Products &gt; Edit Product
      </div>

      <?php if(isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show">
          <?= $error ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <form action="/PHP-CAFETERA/app/controllers/productController.php" 
            method="post" 
            enctype="multipart/form-data">

        <input type="hidden" name="update_product" value="1">
        <input type="hidden" name="id"             value="<?= $product['id'] ?>">
        <input type="hidden" name="old_image"      value="<?= $product['image'] ?>">

        <div class="row g-4">

          <div class="col-12 col-lg-6">
            <h5 class="mb-3">Product Information</h5>

            <div class="mb-3">
              <label for="name" class="form-label">
                Product Name <span class="req">(Required)</span>
              </label>
              <input
                id="name"
                name="name"
                type="text"
                class="form-control"
                value="<?= htmlspecialchars($product['name']) ?>"
                required
              >
            </div>

            <div class="mb-3">
              <label for="category" class="form-label">
                Category <span class="req">(Required)</span>
              </label>
              <select id="category" name="category_id" class="form-select" required>
                <option value="" disabled>Choose...</option>
                <?php
                  $categoryController = new CategoryController();
                  $cat_list = $categoryController->showAllCategories();
                  foreach($cat_list as $cat):
                    $selected = ($cat['id'] == $product['category_id']) ? 'selected' : '';
                    echo '<option value="' . $cat['id'] . '" ' . $selected . '>' 
                          . htmlspecialchars($cat['name']) 
                        . '</option>';
                  endforeach;
                ?>
              </select>
            </div>

            <div class="mb-3">
              <label for="price" class="form-label">
                Selling Price <span class="req">(Required)</span>
              </label>
              <div class="input-group" style="max-width: 320px;">
                <span class="input-group-text">EGP</span>
                <input
                  id="price"
                  name="price"
                  type="number"
                  step="0.01"
                  min="0"
                  class="form-control"
                  value="<?= htmlspecialchars($product['price']) ?>"
                  required
                >
              </div>
            </div>

            <div class="d-flex gap-2 mt-4">
              <button type="submit" class="btn btn-success px-4">Update Product</button>
              <a href="/PHP-CAFETERA/app/views/admin/products.php" 
                 class="btn btn-outline-secondary px-4">
                Cancel
              </a>
            </div>
          </div>

          <div class="col-12 col-lg-6">
            <h5 class="mb-3">Media</h5>

            <div class="upload-box">
              <img 
                id="preview" 
                src="/PHP-CAFETERA/public/assets/images/products/<?= htmlspecialchars($product['image']) ?>" 
                alt="Current Image"
                style="max-width:100%; max-height:220px; border-radius:10px;"
              >
              <label class="upload-label" for="image" id="uploadLabel" style="display:none;">
                <div class="upload-hint">Click to change image</div>
              </label>
              <input id="image" name="image" type="file" accept="image/*">
            </div>
            <small class="text-muted">Leave empty to keep current image</small>
          </div>

        </div>
      </form>
    </div>
  </main>
</div>

<script>
const imageInput  = document.getElementById('image');
const preview     = document.getElementById('preview');

preview.style.cursor = 'pointer';
preview.onclick = () => imageInput.click();

imageInput.addEventListener('change', function() {
  const file = this.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      preview.src = e.target.result;
    }
    reader.readAsDataURL(file);
  }
});
</script>