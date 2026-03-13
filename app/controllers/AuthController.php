<?php
class AuthController {
    
    public function login() {
        require_once __DIR__ . "/../config/Database.php";
        require_once __DIR__ . "/../models/User.php";

        $database = new Database();
        $db = $database->connect();

        // Process login
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $email = trim($_POST["email"] ?? "");
            $password = $_POST["password"] ?? "";

            if (empty($email) || empty($password)) {
                header("Location: /login?error=Email and password required");
                exit();
            }

            $userModel = new User($db);
            $user = $userModel->getByEmail($email);

            if (!$user || !password_verify($password, $user["password"])) {
                header("Location: /login?error=Invalid email or password");
                exit();
            }

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["user_role"] = $user["role"];

            if ($user["role"] === "admin") {
                header("Location: /admin/users");
            } else {
                header("Location: /");
            }
            exit();
        }

        // Render login view
        $error = $_GET["error"] ?? null;
        $success = $_GET["success"] ?? null;
        require_once __DIR__ . "/../views/auth/login.php";
    }

    public function logout() {
        session_destroy();
        header("Location: /login?success=Logged out successfully");
        exit();
    }

    public function register() {
        require_once __DIR__ . "/../config/Database.php";
        require_once __DIR__ . "/../models/User.php";
        require_once __DIR__ . "/../models/Room.php";

        $database = new Database();
        $db = $database->connect();

        $errors = [];
        $old = [];

        // Check if form was submitted
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $old = $_POST; // keep old input

            $name = trim($_POST["name"]);
            $email = trim($_POST["email"]);
            $password = $_POST["password"];
            $confirm = $_POST["confirm_password"];
            $room = trim($_POST["room_no"]);
            $ext = trim($_POST["ext"]);

            // -------- Name Validation --------
            if (!preg_match("/^[A-Za-z ]{3,30}$/", $name)) {
                $errors["name"] = "Name must be 3-30 letters and spaces only";
            }

            // -------- Email Validation --------
            if (!preg_match("/^[\w\-\.]+@([\w\-]+\.)+[\w\-]{2,4}$/", $email)) {
                $errors["email"] = "Invalid email format";
            }

            // -------- Password Validation --------
            if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/", $password)) {
                $errors["password"] =
                    "Password must be minimum 8 characters with letters and numbers";
            }

            // -------- Confirm Password --------
            if ($password !== $confirm) {
                $errors["confirm_password"] = "Passwords do not match";
            }

            // -------- Room --------
            if (!preg_match("/^[1-9][0-9]{0,3}$/", $room)) {
                $errors["room_no"] = "Room number must be between 1 and 9999";
            }

            // -------- Extension --------
            if (!empty($ext) && !preg_match("/^\d{2,5}$/", $ext)) {
                $errors["ext"] = "Extension must be 2-5 digits only";
            }

            // -------- Profile Picture --------
            $uploadedFileName = null;
            if (!empty($_FILES["profile_picture"]["name"])) {
                $allowed = ["jpg", "jpeg", "png"];
                $fileName = $_FILES["profile_picture"]["name"];
                $extFile = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                if (!in_array($extFile, $allowed)) {
                    $errors["profile_picture"] = "Only JPG, JPEG, PNG are allowed";
                } elseif ($_FILES["profile_picture"]["size"] > 5000000) {
                    $errors["profile_picture"] = "File size must be less than 5MB";
                } elseif ($_FILES["profile_picture"]["error"] !== 0) {
                    $errors["profile_picture"] = "Error uploading file";
                } else {
                    // Generate unique filename
                    $uploadedFileName = time() . "_" . uniqid() . "." . $extFile;
                }
            }

            // -------- Success --------
            if (empty($errors)) {
                // Look up room ID by room name/number
                $roomModel = new Room($db);
                $roomData = $roomModel->getByName($room);

                if (!$roomData) {
                    $errors["room_no"] = "Room number does not exist";
                } else {
                    // Move uploaded file if exists
                    if ($uploadedFileName) {
                        $uploadDir = __DIR__ . "/../../public/uploads/";
                        move_uploaded_file(
                            $_FILES["profile_picture"]["tmp_name"],
                            $uploadDir . $uploadedFileName,
                        );
                    }

                    // Create user in database
                    $user = new User($db);
                    $user->name = $name;
                    $user->email = $email;
                    $user->password = password_hash($password, PASSWORD_DEFAULT);
                    $user->room_id = $roomData["id"];
                    $user->image = $uploadedFileName;
                    $user->role = "user";
                    $user->save();

                    $success = "User added successfully!";
                    $old = []; // clear old input
                }
            }
        }

        // Render view is handled by add_user.php
    }

    public function forgotPassword() {
        require_once __DIR__ . "/../config/Database.php";
        require_once __DIR__ . "/../models/User.php";

        $database = new Database();
        $db = $database->connect();

        // Process forgot password form
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $email = trim($_POST["email"] ?? "");

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                header("Location: /forgot-password?error=Please enter a valid email address");
                exit();
            }

            $userModel = new User($db);
            $user = $userModel->getByEmail($email);

            if (!$user) {
                header("Location: /forgot-password?error=No account found with this email address");
                exit();
            }

            // Set session for password reset
            $_SESSION["reset_email"] = $email;
            header("Location: /reset-password?success=You can now reset your password");
            exit();
        }

        // Render forgot password view
        $error = $_GET["error"] ?? null;
        $success = $_GET["success"] ?? null;
        require_once __DIR__ . "/../views/auth/forgot_password.php";
    }

    public function resetPassword() {
        require_once __DIR__ . "/../config/Database.php";
        require_once __DIR__ . "/../models/User.php";

        $database = new Database();
        $db = $database->connect();

        // Check if user has permission to reset password
        if (!isset($_SESSION["reset_email"])) {
            header("Location: /forgot-password?error=Please request password reset first");
            exit();
        }

        $email = $_SESSION["reset_email"];
        $userModel = new User($db);
        $user = $userModel->getByEmail($email);

        if (!$user) {
            unset($_SESSION["reset_email"]);
            header("Location: /forgot-password?error=User not found");
            exit();
        }

        // Process reset password form
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $password = $_POST["password"] ?? "";
            $confirm = $_POST["confirm_password"] ?? "";

            if (strlen($password) < 8) {
                header("Location: /reset-password?error=Password must be at least 8 characters");
                exit();
            }

            if ($password !== $confirm) {
                header("Location: /reset-password?error=Passwords do not match");
                exit();
            }

            if ($userModel->updatePassword($user["id"], $password)) {
                unset($_SESSION["reset_email"]);
                header("Location: /login?success=Password reset successfully. Please login with your new password.");
                exit();
            } else {
                header("Location: /reset-password?error=Failed to reset password");
                exit();
            }
        }

        // Render reset password view
        $error = $_GET["error"] ?? null;
        $success = $_GET["success"] ?? null;
        require_once __DIR__ . "/../views/auth/reset_password.php";
    }
}
?>
