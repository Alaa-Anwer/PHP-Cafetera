<?php
$error = $_GET["error"] ?? null;
$success = $_GET["success"] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password - Cafeteria</title>
<link rel="stylesheet" href="/assets/css/login.css">
</head>
<?php include __DIR__ . "/../layouts/jsCDN.php"; ?>
<body class="login-page">

<div class="container d-flex justify-content-center align-items-center min-vh-100">

    <div class="login-box">

        <h1 class="fw-bold mb-1">Reset Password</h1>
        <p class="text-muted mb-4">Enter your new password</p>

        <?php if ($error) { ?>
            <div class="alert alert-danger text-center">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php } ?>

        <?php if ($success) { ?>
            <div class="alert alert-success text-center">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php } ?>

        <form method="post">

            <div class="mb-3">
                <label class="form-label">New Password</label>
                <input
                    type="password"
                    name="password"
                    class="form-control custom-input"
                    placeholder="Enter new password"
                    required>
            </div>

            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input
                    type="password"
                    name="confirm_password"
                    class="form-control custom-input"
                    placeholder="Confirm new password"
                    required>
            </div>

            <button class="btn login-btn w-100">
                Reset Password
            </button>

        </form>
    </div>
</div>

</body>
</html>
