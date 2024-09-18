<?php
session_start();
include("includes/config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $name = $_POST['name'];

    // Check if username or email already exists
    $query = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        echo "Username or Email already exists.";
    } else {
        // Insert new user
        $query = "INSERT INTO users (username, email, password, name) VALUES ('$username', '$email', '$password', '$name')";
        mysqli_query($conn, $query);
        header("Location: login.php");
        die;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<body>
    <!-- <h1>Register</h1>
    <form method="POST">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br><br>
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        <label for="name">Full Name:</label><br>
        <input type="text" id="name" name="name" required><br><br>
        <input type="submit" value="Register">
    </form>
    <p>Already have an account? <a href="login.php">Login here</a>.</p> -->


    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Register</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="register.php">
                        
                        <div class="mb-3">
                                <input type="text" class="form-control" id="name" placeholder="Your name" name="name" required>
                            </div>
                        
                        <div class="mb-3">
                                <input type="text" class="form-control" id="username" placeholder="Username" name="username" required>
                            </div>

                            <div class="mb-3">
                                <input type="text" class="form-control" id="email" placeholder="Email" name="email" required>
                            </div>

                            <div class="mb-3">
                                <input type="password" class="form-control" id="password" placeholder="Password" name="password" required>
                            </div>
                        
                        <div class="d-grid gap-2 col-15 mx-auto">
                          <button type="submit" class="btn btn-primary shadow-sm" type="button">Register</button>
                        </div>
                        </form>
                    </div>
                    <div class="text-center">
                        <p>Already have an account? <a href="login.php">Login here</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
