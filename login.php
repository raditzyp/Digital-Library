<?php
session_start();
include("includes/config.php");

$showError = false; // Variable to control toast visibility

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            header("Location: user/index.php");
            die;
        }
    }
    $showError = true;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .toast-custom-bg {
            background-color: #D24545;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <!-- Toast Notification -->
                <div class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 11; width: 100%;">
                    <div id="errorToast" class="toast align-items-center toast-custom-bg border-0 mx-auto" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body text-white">
                                Invalid username or password.
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
                <div class="card mt-5">
                    <div class="card-header text-center">
                        <h4>Login</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="login.php">
                            <div class="mb-3">
                                <input type="text" class="form-control" id="username" placeholder="Username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" class="form-control" id="password" placeholder="Password" name="password" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 d-flex justify-content-left">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" value="" id="loginCheck" checked />
                                        <label class="form-check-label" for="loginCheck"> Remember me </label>
                                    </div>
                                </div>
                            </div>
                            <div class="d-grid gap-2 col-15 mx-auto">
                                <button type="submit" class="btn btn-primary shadow-sm">Login</button>
                            </div>
                        </form>
                    </div>
                    <div class="text-center">
                        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($showError): ?>
                var toast = new bootstrap.Toast(document.getElementById('errorToast'));
                toast.show();
            <?php endif; ?>
        });
    </script>
</body>
</html>
