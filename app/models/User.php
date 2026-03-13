<?php
class User
{
    private $db;
    private $table = "users";

    public $id;
    public $name;
    public $email;
    public $password;
    public $room_id;
    public $image;
    public $role;
    public $created_at;

    public function __construct($db)
    {
        $this->db = $db;
        $this->created_at = date("Y-m-d H:i:s");
    }

    function save()
    {
        $query =
            "INSERT INTO " .
            $this->table .
            " (name, email, password, room_id, image, role, created_at) VALUES (:name, :email, :password, :room_id, :image, :role, :created_at)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":room_id", $this->room_id);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":role", $this->role);
        $stmt->bindParam(":created_at", $this->created_at);
        return $stmt->execute();
    }

    function getAll()
    {
        $query = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function getByEmail($email)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    function updatePassword($id, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE " . $this->table . " SET password = :password WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":password", $hashedPassword);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>
