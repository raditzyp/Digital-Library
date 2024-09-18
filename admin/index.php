<?php
session_start();
include("../includes/config.php");
include("../includes/functions.php");

$admin_data = check_admin_login($conn);

$user_data = check_login($conn);

// Query to count total books, total users, and active borrowings
$query_total_books = "SELECT COUNT(*) as total_books FROM buku";
$result_total_books = mysqli_query($conn, $query_total_books);
$total_books = mysqli_fetch_assoc($result_total_books)['total_books'];

$query_total_users = "SELECT COUNT(*) as total_users FROM users";
$result_total_users = mysqli_query($conn, $query_total_users);
$total_users = mysqli_fetch_assoc($result_total_users)['total_users'];

$query_total_borrowings = "SELECT COUNT(*) as total_borrowings FROM pinjam";
$result_total_borrowings = mysqli_query($conn, $query_total_borrowings);
$total_borrowings = mysqli_fetch_assoc($result_total_borrowings)['total_borrowings'];

$query = "SELECT buku.*, kategori.nama_kategori FROM buku JOIN kategori ON buku.id_kategori = kategori.id_kategori";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="index.php">Home</a>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Manage
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="manage_books.php">Book</a></li>
            <li><a class="dropdown-item" href="manage_categories.php">Category</a></li>
            <li><a class="dropdown-item" href="manage_users.php">User</a></li>
          </ul>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="transactions.php">Transactions</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container p-5 mt-5">
    <h2>Welcome to Admin Panel <?php echo $admin_data['username']; ?></h2>

  <div class="container mt-4">

  <div class="row row-cols-1 mb-3 row-cols-md-3 g-4">
        <div class="col">
                <div class="container">
                    <h4 class="card-title">Total Books</h4>
                    <p class="card-text fs-4"><?php echo $total_books; ?></p>
                </div>
        </div>
        <div class="col">
                <div class="container">
                    <h4 class="card-title">Total Users</h4>
                    <p class="card-text fs-4"><?php echo $total_users; ?></p>
                </div>
        </div>
        <div class="col">
                <div class="container">
                    <h4 class="card-title">Total Borrowings</h4>
                    <p class="card-text fs-4"><?php echo $total_borrowings; ?></p>
                </div>
        </div>
    </div>

  <div class="row row-cols-1 row-cols-md-3 g-4">
    <div class="col">
      <div class="card text-white bg-primary mb-3" style="max-width: 18rem;">
        <div class="card-body shadow">
          <h5 class="card-title">Manage Books</h5>
          <p class="card-text"></p>
          <a href="manage_books.php" class="btn btn-light">Go somewhere</a>
        </div>
      </div>
    </div>
    <div class="col">
      <div class="card text-white bg-primary mb-3" style="max-width: 18rem;">
        <div class="card-body shadow">
          <h5 class="card-title">Manage Categories</h5>
          <p class="card-text"></p>
        <a href="manage_categories.php" class="btn btn-light">Go somewhere</a>
        </div>
      </div>
    </div>
    <div class="col">
      <div class="card text-white bg-primary mb-3" style="max-width: 18rem;">
        <div class="card-body shadow">
          <h5 class="card-title">Manage User</h5>
          <p class="card-text"></p>
          <a href="manage_users.php" class="btn btn-light">Go somewhere</a>
        </div>
      </div>
    </div>
  </div>
</div>


  <div class="container mx-auto">
    <h4 class="mt-5">Featured Books</h4>
    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Publisher</th>
                <th>Year</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $row['judul_buku']; ?></td>
                <td><?php echo $row['pengarang']; ?></td>
                <td><?php echo $row['penerbit']; ?></td>
                <td><?php echo $row['tahun_terbit']; ?></td>
                <td><?php echo ($row['status'] == 0) ? 'Available' : 'Borrowed'; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    </div>
    </div>

    <footer class="bg-light text-center text-lg-start mt-5">
  <div class="container p-4">
    <div class="row">
      <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
        <h5 class="text-uppercase">Digilib</h5>
        <p>
          This is a library management system. You can manage books, categories, and users through this admin panel.
        </p>
      </div>
      <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
        <h5 class="text-uppercase">Contact Us</h5>
        <ul class="list-unstyled mb-0">
          <li>
            <a href="#!" class="text-dark">Email: support@digilib.com</a>
          </li>
          <li>
            <a href="#!" class="text-dark">Phone: +1 234 567 890</a>
          </li>
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
