<?php
session_start();
include("includes/config.php");
include("includes/functions.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $query = "SELECT * FROM admin WHERE username = '$username' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $admin_data = mysqli_fetch_assoc($result);

            if (password_verify($password, $admin_data['password'])) {
                $_SESSION['admin_id'] = $admin_data['id_admin'];
                header("Location: admin/index.php");
                die;
            }
        }

        echo "Wrong username or password!";
    } else {
        echo "Please enter valid information!";
    }
}
?>

<!-- <h1>Admin Login</h1>
<form method="POST">
    <label for="username">Username:</label><br>
    <input type="text" id="username" name="username"><br>
    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password"><br><br>
    <input type="submit" value="Login">
</form> -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <script src="assets/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Admin Login</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="admin_login.php">
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
                          <button type="submit" class="btn btn-primary shadow-sm" type="button">Login</button>
                        </div>
                        </form>
                    </div>
                    <div class="text-center">
                        <p>as admin you don't have an account? <a href="register_admin.php">Register here</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>