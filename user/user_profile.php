<?php
session_start();
include("../includes/config.php");
include("../includes/functions.php");

$user_data = check_login($conn);

// Get search term from GET request
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = $user_data['id_user'];
    $username = $_POST['username'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET username = '$username', email = '$email', password = '$hashed_password' WHERE id_user = '$id_user'";
    } else {
        $query = "UPDATE users SET username = '$username', email = '$email' WHERE id_user = '$id_user'";
    }

    if (mysqli_query($conn, $query)) {
        $user_data['username'] = $username;
        $user_data['name'] = $name;
        $user_data['email'] = $email;
        $_SESSION['user_data'] = $user_data;
        $notification = "Profile updated successfully.";
    } else {
        $notification = "Error updating profile.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Profile</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
        }

        .navbar-nav .nav-item {
            margin-right: 10px;
        }

        .navbar-nav .nav-link {
            font-weight: 500;
        }

        .navbar-brand {
            font-weight: 700;
            position: relative;
            padding-right: 20px;
        }
        .navbar-brand::after {
            content: '';
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 2px; /* Lebar garis */
            height: 30px; /* Tinggi garis, sesuaikan dengan ukuran navbar */
            background-color: #fff; /* Warna garis */
        }

        /* Custom CSS to adjust the navbar */
        .navbar {
            padding: 0.5rem 1rem;
        }

        .navbar-nav {
            margin-left: auto;
        }
        .container-nav {
            max-width: 100%;
            padding-left: 0;
            padding-right: 0;
        }   
        
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark shadow fixed-top bg-primary">
  <div class="container container-nav">
    <a class="navbar-brand" href="#">DiGiLiB</a>
    <hr style="margin: 0 10px; border: 1px solid #fff;">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <form class="d-flex me-auto" method="GET" action="book.php">
        <input class="form-control me-2" type="search" placeholder="Search books..." name="search"
          value="<?php echo $search; ?>">
        <button class="btn btn-outline-light" type="submit">Search</button>
      </form>
      <ul class="navbar-nav ms-auto mb-8 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="index.php">Home</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="categoryDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Category
          </a>
          <ul class="dropdown-menu" aria-labelledby="categoryDropdown">
              <?php 
              $categories_query = "SELECT * FROM kategori";
              $categories_result = mysqli_query($conn, $categories_query);
              if(mysqli_num_rows($categories_result) > 0) {
                  while ($category = mysqli_fetch_assoc($categories_result)) {
                      ?>
                      <li><a class="dropdown-item" href="book.php?category=<?php echo $category['id_kategori']; ?>"><?php echo $category['nama_kategori']; ?></a></li>
                      <?php
                  }
              } else {
                  echo "<li><a class='dropdown-item' href='#'>No categories found</a></li>";
              }
              ?>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Library
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="read_book.php">Reading List</a></li>
            <li><a class="dropdown-item" href="favorite_book.php">Favorite Book</a></li>
            <li><a class="dropdown-item" href="history.php">History</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link active dropdown-toggle" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" href="user_profile.php">
            Profile
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item active" href="user_profile.php">My Profile</a></li>
            <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container p-5 mt-5">
    <h1>Manage Profile</h1>
    <?php if (isset($notification)) { ?>
        <div class="alert alert-info">
            <?php echo $notification; ?>
        </div>
    <?php } ?>

    <form method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" value="<?php echo $user_data['username']; ?>">
        </div>
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo $user_data['name']; ?>">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $user_data['email']; ?>">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">New Password (leave blank to keep current password)</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>

<footer class="bg-light text-center text-lg-start mt-5">
        <div class="container p-4">
            <div class="row">
                <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                    <h5 class="text-uppercase">Digilib</h5>
                    <p>This is a library management system. You can manage books, categories, and users through this admin panel.</p>
                </div>
                <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                    <h5 class="text-uppercase">Contact Us</h5>
                    <ul class="list-unstyled mb-0">
                        <li><a href="#!" class="text-dark">Email: support@digilib.com</a></li>
                        <li><a href="#!" class="text-dark">Phone: +1 234 567 890</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
            &copy; 2023 Digilib. All rights reserved.
        </div>
    </footer>

</body>
</html>
