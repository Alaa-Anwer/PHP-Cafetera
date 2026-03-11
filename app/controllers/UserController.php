<?php
    class UserController {
        private $user;

        public function __construct($db) {
            require_once __DIR__ . '/../models/User.php';
            $this->user = new User($db);
        }

        public function index() {
            $users = $this->user->getAll();
            require_once __DIR__ . '/../views/admin/users.php';
        }

        public function show($id) {
            $user = $this->user->getById($id);
            if (!$user) {
                echo "User not found!";
                return;
            }
        }

        public function create() {
            require_once __DIR__ . '/../views/admin/add_user.php';
        }

        public function delete($id) {
            return $this->user->delete($id);
        }
    }
?>