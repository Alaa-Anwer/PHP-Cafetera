<?php
require_once __DIR__ . "/../models/Category.php";

class CategoryController{

public Category $categoryModel;

function __construct(){
$this->categoryModel = new Category();
}

function createCategory(){

if($_SERVER['REQUEST_METHOD'] == 'POST'){

$name = trim($_POST['name']) ?? '';




if(empty($name)){
    echo "empty";
    $error = "Category name Can't be Empty!";
    require_once __DIR__ . '/../views/admin/addCategory.php';
    return;


}         


if(strlen($name) > 100){
    $error = "Name must be less than 100 characters";
    require_once __DIR__ . '../views/admin/addCategory.php';
    return;

}

if($this->categoryModel->create($name)){
    header("Location: /PHP-CAFETERA/app/views/admin/add_product.php?msg=category_Added");
    exit();
}

else{
    header("Location: /PHP-CAFETERA/app/views/admin/add_product.php?msg=category_exists");
    
}

}





}

function showAllCategories(){

$all = $this->categoryModel->getAll();
return $all;
}

}

if ($_SERVER['REQUEST_METHOD'] == 'POST'  && isset($_POST['add_category'])) {
    $controller = new CategoryController();
    $controller->createCategory();
}