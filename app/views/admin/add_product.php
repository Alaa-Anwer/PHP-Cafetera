<link rel="stylesheet" href="/PHP-Cafetera/public/assets/css/bootstrap.css">
<link rel="stylesheet" href="/PHP-Cafetera/public/assets/css/sidebar.css">

<style>
  .panel{
    background: #f7f1ea;
    border-radius: 16px;
    border: 1px solid rgba(0,0,0,.08);
    padding: 22px;
  }
  .req{ color:#b42318; font-weight:600; }

  .upload-box{
    border: 2px dashed rgba(0,0,0,.25);
    border-radius: 14px;
    background: rgba(255,255,255,.35);
    min-height: 240px;
    display:flex;
    align-items:center;
    justify-content:center;
    text-align:center;
    padding: 18px;
    position: relative;
  }
  .upload-box input[type="file"]{ display:none; }
  .upload-label{ cursor:pointer; width:100%; }
  .upload-hint{ color: rgba(0,0,0,.65); font-size:.95rem; }

  #preview {
    max-width: 100%;
    max-height: 220px;
    border-radius: 10px;
    display: none;
    margin: 0 auto;
  }

  .modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0; top: 0;
    width: 100%; height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.5);
  }
  .modal-content {
    background-color: #fff;
    margin: 10% auto;
    padding: 20px;
    border-radius: 10px;
    width: 50%;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    position: relative;
  }
  .close {
    color: #aaa;
    position: absolute;
    top: 10px; right: 20px;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
  }
  .close:hover { color: #000; }
</style>

<div class="app">
  <?php
    $active = 'products';
    require_once __DIR__ . '/../layouts/sidebar.php';
    require_once __DIR__ . '/../../controllers/categoryController.php';
    require_once __DIR__ . '/../../controllers/productController.php';
  ?>

  <main class="content">
    <div class="panel">
      <h2 class="mb-1">Add New Product</h2>
      <div class="text-muted small mb-4">Dashboard &gt; Products &gt; Add New Product</div>

      <?php if(isset($_GET['msg'])): ?>
        <?php switch($_GET['msg']):
          case 'product_added': ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              Product Added Successfully!
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php break; ?>

          <?php case 'category_added': ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
               Category Added Successfully!
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php break; ?>

          <?php case 'category_exists': ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
               Category Already Exists!
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php break; ?>

        <?php endswitch; ?>
      <?php endif; ?>

      <?php if(isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
           <?= $error ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <form action="/PHP-CAFETERA/app/controllers/productController.php" 
            method="post" 
            enctype="multipart/form-data">
              <input type="hidden" name="add_product" value="1">

        <div class="row g-4">
            

          <div class="col-12 col-lg-6">
            <h5 class="mb-3">Product Information</h5>

            <div class="mb-3">
              <label for="product_name" class="form-label">
                Product Name <span class="req">(Required)</span>
              </label>
              <input
                id="product_name"
                name="name"
                type="text"
                class="form-control"
                placeholder="e.g., Tea"
                value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>"
                required
              >
            </div>

            <div class="mb-3">
              <label for="category" class="form-label">
                Category <span class="req">(Required)</span>
              </label>
              <select id="category" name="category_id" class="form-select" required>
                <option value="" selected disabled>Choose...</option>
                <?php
                  $categoryController = new CategoryController();
                  $cat_list = $categoryController->showAllCategories();
                  foreach($cat_list as $cat){
                    $selected = (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : '';
                    echo '<option value="' . $cat["id"] . '" ' . $selected . '>' . $cat["name"] . '</option>';
                  }
                ?>
              </select>
              <div class="mt-2">
                <a href="#" id="openModalLink" class="link-success text-decoration-underline">
                  Add New Category
                </a>
              </div>
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
                  placeholder="0.00"
                  value="<?= isset($_POST['price']) ? htmlspecialchars($_POST['price']) : '' ?>"
                  required
                >
              </div>
            </div>

            <div class="d-flex gap-2 mt-4">
              <button type="submit" class="btn btn-success px-4">Save Product</button>
              <button type="reset" class="btn btn-outline-secondary px-4">Reset</button>
            </div>
          </div>

          <div class="col-12 col-lg-6">
            <h5 class="mb-3">Media</h5>

            <div class="upload-box">
              <img id="preview" src="" alt="Preview">

              <label class="upload-label" for="image" id="uploadLabel">
                <div class="fw-semibold mb-2">
                  Product Image <span class="req">(Required)</span>
                </div>
                <div class="upload-hint">Click to upload</div>
                <div class="upload-hint small">PNG / JPG / WEBP — Max 2MB</div>
              </label>
              <input id="image" name="image" type="file" accept="image/*" required>
            </div>
          </div>

        </div>
      </form>
    </div>

    <div id="myModal" class="modal">
      <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Add New Category</h2>
        <form action="/PHP-CAFETERA/app/controllers/categoryController.php" method="post">
              <input type="hidden" name="add_category" value="1">

          <label for="cat_name" class="form-label">Name:</label>
          <input type="text" name="name" id="cat_name" class="form-control" required>
          <br>
          <button class="btn btn-success" type="submit">ADD</button>
        </form>
      </div>
    </div>

  </main>
</div>

<script>
const modal   = document.getElementById("myModal");
const link    = document.getElementById("openModalLink");
const spanClose = document.getElementsByClassName("close")[0];

link.onclick = function(e) {
  e.preventDefault();
  modal.style.display = "block";
}
spanClose.onclick = function() {
  modal.style.display = "none";
}
window.onclick = function(e) {
  if (e.target == modal) modal.style.display = "none";
}

const imageInput  = document.getElementById('image');
const preview     = document.getElementById('preview');
const uploadLabel = document.getElementById('uploadLabel');

imageInput.addEventListener('change', function() {
  const file = this.files[0];

  if (file) {
    const reader = new FileReader();

    reader.onload = function(e) {
      preview.src     = e.target.result;
      preview.style.display = 'block';      
      uploadLabel.style.display = 'none';   
    }

    reader.readAsDataURL(file);
  }
});
</script>