<?php
require_once __DIR__ . '/../config/Database.php';

class category{

private $conn;
private $table = 'categories';

public $id;
public $name;

public function __construct(){
    // connect tp database
    $db = new Database();
    $this->conn = $db->connect();
}

public function getAll(){
    $sql = "select * from {$this->table} order by id ASC";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
    

}

public function getByID($id){
    $sql = "select * from {$this->table} where id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch();
}


public function create($name){
    $this->name = htmlspecialchars(strip_tags(trim($name)));
    $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE LOWER(name) = LOWER(:name)";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(":name", $this->name);
    $stmt->execute();
    $row = $stmt->fetch();
    if($row['total'] == 0 and $this->name){
    $query = "insert into {$this->table} (name) values(:name)";
    $stm = $this->conn->prepare($query);
    $stm->bindParam(":name", $this->name);
    return $stm->execute();
    }
    else{
        return "Category already exists!";
        }

}

// public function hasProducts($name){
//     $sql = "select count(*) as total from products where name = :name";
//     $stmt = this->conn->prepare($query);
//     $stmt->bindParam(":name", $name);
//     $stmt->execute();
//     $row=$stm->fetch();
//     return $row['total'] > 0;
// }



}



