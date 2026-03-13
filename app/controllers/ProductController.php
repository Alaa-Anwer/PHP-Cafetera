<?php
require_once __DIR__ . '/../models/Product.php';

class ProductController {

    public Product $productModel;
    private string $uploadDir = __DIR__ . '/../../public/assets/images/products/';

    public function __construct() {
        $this->productModel = new Product();
    }

    public function createProduct() {

        $name        = trim($_POST['name']        ?? '');
        $category_id = trim($_POST['category_id'] ?? '');
        $price       = trim($_POST['price']       ?? '');

        if (empty($name)) {
            $error = "Product name can't be empty!";
            require __DIR__ . '/../views/admin/add_product.php';
            return;
        }
        if (strlen($name) > 100) {
            $error = "Name must be less than 100 characters!";
            require __DIR__ . '/../views/admin/add_product.php';
            return;
        }
        if (empty($category_id)) {
            $error = "Please select a category!";
            require __DIR__ . '/../views/admin/add_product.php';
            return;
        }
        if (empty($price) || !is_numeric($price) || $price < 0) {
            $error = "Please enter a valid price!";
            require __DIR__ . '/../views/admin/add_product.php';
            return;
        }
        if (empty($_FILES['image']['name'])) {
            $error = "Please upload an image!";
            require __DIR__ . '/../views/admin/add_product.php';
            return;
        }

        $image = $this->uploadImage();
        if (!$image) {
            $error = "Image upload failed! Only PNG/JPG/WEBP under 2MB allowed.";
            require __DIR__ . '/../views/admin/add_product.php';
            return;
        }

        if ($this->productModel->create($name, (int)$category_id, (float)$price, $image)) {
            header("Location: /PHP-CAFETERA/app/views/admin/add_product.php?msg=product_added");
            exit();
        } else {
            $error = "Failed to add product!";
            require __DIR__ . '/../views/admin/add_product.php';
        }
    }

    public function getAllProducts(): array {
        return $this->productModel->getAll();
    }

    public function getProductById(int $id): array|false {
        return $this->productModel->getById($id);
    }

    public function updateProduct() {

        $id          = (int)($_POST['id']          ?? 0);
        $name        = trim($_POST['name']         ?? '');
        $category_id = trim($_POST['category_id']  ?? '');
        $price       = trim($_POST['price']        ?? '');
        $old_image   = trim($_POST['old_image']    ?? '');

        if (empty($name)) {
            $error   = "Product name can't be empty!";
            $product = $this->productModel->getById($id);
            require __DIR__ . '/../views/admin/edit_product.php';
            return;
        }
        if (empty($category_id)) {
            $error   = "Please select a category!";
            $product = $this->productModel->getById($id);
            require __DIR__ . '/../views/admin/edit_product.php';
            return;
        }
        if (empty($price) || !is_numeric($price) || $price < 0) {
            $error   = "Please enter a valid price!";
            $product = $this->productModel->getById($id);
            require __DIR__ . '/../views/admin/edit_product.php';
            return;
        }

        if (!empty($_FILES['image']['name'])) {
            $image = $this->uploadImage();
            if (!$image) {
                $error   = "Image upload failed!";
                $product = $this->productModel->getById($id);
                require __DIR__ . '/../views/admin/edit_product.php';
                return;
            }
            $oldPath = $this->uploadDir . $old_image;
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        } else {
            $image = $old_image;
        }

        if ($this->productModel->update($id, $name, (int)$category_id, (float)$price, $image)) {
            header("Location: /PHP-CAFETERA/app/views/admin/products.php?msg=product_updated");
            exit();
        } else {
            $error   = "Failed to update product!";
            $product = $this->productModel->getById($id);
            require __DIR__ . '/../views/admin/edit_product.php';
        }
    }

    public function deleteProduct() {

        $id      = (int)($_GET['id']    ?? 0);
        $image   = $_GET['image']       ?? '';

        if ($id <= 0) {
            header("Location: /PHP-CAFETERA/app/views/admin/products.php?msg=error");
            exit();
        }

        // delete image file from server
        $imagePath = $this->uploadDir . $image;
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        if ($this->productModel->delete($id)) {
            header("Location: /PHP-CAFETERA/app/views/admin/products.php?msg=product_deleted");
            exit();
        } else {
            header("Location: /PHP-CAFETERA/app/views/admin/products.php?msg=error");
            exit();
        }
    }

    private function uploadImage(): string|false {

        $file    = $_FILES['image'];
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSize = 2 * 1024 * 1024;

        if (!in_array($file['type'], $allowed)) return false;
        if ($file['size'] > $maxSize)           return false;

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }

        $ext     = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newName = uniqid('product_', true) . '.' . $ext;
        $dest    = $this->uploadDir . $newName;

        if (move_uploaded_file($file['tmp_name'], $dest)) {
            return $newName;
        }
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $controller = new ProductController();
    $controller->createProduct();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $controller = new ProductController();
    $controller->updateProduct();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_product'])) {
    $controller = new ProductController();
    $controller->deleteProduct();
}