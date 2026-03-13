<?php
require_once __DIR__ . "/../../config/Database.php";
require_once __DIR__ . "/../../models/User.php";

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

$error = $_GET["error"] ?? null;
$success = $_GET["success"] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cafeteria Login</title>
<link rel="stylesheet" href="/assets/css/login.css">
</head>
<?php include __DIR__ . "/../layouts/jsCDN.php"; ?>
<body class="login-page">

<div class="container d-flex justify-content-center align-items-center min-vh-100">

    <div class="login-box">

        <h1 class="fw-bold mb-1">Login</h1>
        <p class="text-muted mb-4">Hi, Welcome back 👋</p>

        <?php if ($error) { ?>
            <div class="alert alert-danger text-center">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php } ?>

        <form method="post">

            <!-- Email -->
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input
                    type="email"
                    name="email"
                    class="form-control custom-input"
                    placeholder="E.g. johndoe@email.com"
                    required>
            </div>

            <!-- Password -->
            <div class="mb-2">
                <label class="form-label">Password</label>
                <input
                    type="password"
                    name="password"
                    class="form-control custom-input"
                    placeholder="Enter your password"
                    required>
            </div>

            <!-- Remember + Forgot -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="/forgot-password" class="forgot-link">Forgot Password?</a>
            </div>

            <!-- Login Button -->
            <button class="btn login-btn w-100">
                Login
            </button>

        </form>
    </div>
</div>

</body>
</html>
