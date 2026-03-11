<?php
    class User {
        private $db;
        private $table = 'users';

        public $id;
        public $name;
        public $email;  
        public $password;
        public $room_id;
        public $thumbnail;
        public $role;
        public $created_at;

        public function __construct($db) {
            $this->db = $db;
            $this->created_at = date('Y-m-d H:i:s');
        }

        function getAll() {
            $query = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        function getById($id) {
            $query = "SELECT * FROM " . $this->table . "WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        function delete($id) {
            $query = "DELETE FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        }
        // Update user...
    }
?>