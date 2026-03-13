<?php
require_once __DIR__ . '/../config/database.php';

class Product {

    private PDO $conn;
    private string $table = 'products';

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function create(string $name, int $category_id, float $price, string $image): bool {
        try {
            $sql = "INSERT INTO {$this->table} (name, category_id, price, image) 
                    VALUES (:name, :category_id, :price, :image)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':name',        $name);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->bindParam(':price',       $price);
            $stmt->bindParam(':image',       $image);

            return $stmt->execute();

        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function getAll(): array {
        try {
            $sql = "SELECT products.*, categories.name AS category_name 
                    FROM {$this->table}
                    JOIN categories ON products.category_id = categories.id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    public function getById(int $id): array|false {
        try {
            $sql = "SELECT products.*, categories.name AS category_name 
                    FROM {$this->table}
                    JOIN categories ON products.category_id = categories.id
                    WHERE products.id = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function update(int $id, string $name, int $category_id, float $price, string $image): bool {
        try {
            $sql = "UPDATE {$this->table} 
                    SET name = :name, 
                        category_id = :category_id, 
                        price = :price, 
                        image = :image
                    WHERE id = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id',          $id);
            $stmt->bindParam(':name',        $name);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->bindParam(':price',       $price);
            $stmt->bindParam(':image',       $image);

            return $stmt->execute();

        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function delete(int $id): bool {
        try {
            $sql = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();

        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
