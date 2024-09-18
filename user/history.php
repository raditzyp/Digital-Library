<?php
session_start();
include("../includes/config.php");
include("../includes/functions.php");

// Memeriksa apakah pengguna sudah login
$user_data = check_login($conn);
if (!$user_data) {
    // Jika tidak ada data pengguna yang valid, arahkan ke halaman login atau tindakan lainnya
    header("Location: ../login.php");
    exit();
}
// Get search term from GET request
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : '';

$id_user = $user_data['id_user'];

$query = "SELECT t.*, b.judul_buku, b.pengarang, b.penerbit, b.tahun_terbit FROM transaksi t 
          JOIN buku b ON t.id_buku = b.id_buku 
          WHERE t.id_user = '$id_user'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan History</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
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
          <a class="nav-link active dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Library
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="read_book.php">Reading List</a></li>
            <li><a class="dropdown-item" href="favorite_book.php">Favorite Book</a></li>
            <li><a class="dropdown-item active" href="history.php">History</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" href="user_profile.php">
            Profile
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="user_profile.php">My Profile</a></li>
            <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
<div class="container p-5 mt-5">
    <h1 class="text-center">Loan History</h1>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $row['judul_buku']; ?></h5>
                            <h6 class="card-subtitle mb-2 text-muted">Author: <?php echo $row['pengarang']; ?></h6>
                            <p class="card-text">Publisher: <?php echo $row['penerbit']; ?></p>
                            <p class="card-text">Year: <?php echo $row['tahun_terbit']; ?></p>
                            <p class="card-text">Borrow Date: <?php echo $row['tanggal_peminjaman']; ?></p>
                            <p class="card-text">Return Date: <?php echo $row['tanggal_pengembalian']; ?></p>
                        </div>
                    </div>
                </div>
                <?php } ?>
        <?php } else { ?>
            <div class="col-12">
                <div class="alert alert-secondary text-center">
                    You have not loan history any book yet
                </div>
            </div>
        <?php } ?>
    </div>
</div>
</body>
</html>
