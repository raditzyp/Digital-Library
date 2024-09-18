<?php
session_start();
include("../includes/config.php");
include("../includes/functions.php");

$user_data = check_login($conn);
$notification = '';

// Handling borrow action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['borrow_book'])) {
    $id_buku = $_POST['id_buku'];
    $id_user = $user_data['id_user'];
    $tanggal_peminjaman = date('Y-m-d');
    $tanggal_pengembalian = date('Y-m-d', strtotime($tanggal_peminjaman . ' + 14 days'));

    // Insert into pinjam table
    $query_insert_pinjam = "INSERT INTO pinjam (id_buku, id_user, tanggal_peminjaman, tanggal_pengembalian) 
                            VALUES ('$id_buku', '$id_user', '$tanggal_peminjaman', '$tanggal_pengembalian')";
    // Update the book status
    $query_update_buku = "UPDATE buku SET status = 1 WHERE id_buku = '$id_buku'";

    if (mysqli_query($conn, $query_insert_pinjam) && mysqli_query($conn, $query_update_buku)) {
        $notification = "Buku berhasil dipinjam.";
    } else {
        $notification = "Terjadi kesalahan saat meminjam buku.";
    }
}

// Handling like action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['like_book'])) {
    $id_buku = $_POST['id_buku'];
    $id_user = $user_data['id_user'];

    // Check if the book is already liked by this user
    $query_check_favorite = "SELECT * FROM favorite_buku WHERE id_buku = '$id_buku' AND id_user = '$id_user'";
    $result_check_favorite = mysqli_query($conn, $query_check_favorite);

    if (mysqli_num_rows($result_check_favorite) > 0) {
        // Buku sudah disukai, maka kita akan menghapusnya
        $query_delete_favorite = "DELETE FROM favorite_buku WHERE id_buku = '$id_buku' AND id_user = '$id_user'";
        
        if (mysqli_query($conn, $query_delete_favorite)) {
            $notification = "Buku telah dihapus dari daftar favorit Anda.";
        } else {
            $notification = "Terjadi kesalahan saat menghapus buku dari favorit Anda.";
        }
    } else {
        // Buku belum disukai, maka kita akan menambahkannya
        $query_insert_favorite = "INSERT INTO favorite_buku (id_buku, id_user) VALUES ('$id_buku', '$id_user')";

        if (mysqli_query($conn, $query_insert_favorite)) {
            $notification = "Buku telah ditambahkan ke daftar favorit Anda.";
        } else {
            $notification = "Terjadi kesalahan saat menambahkan buku ke favorit Anda.";
        }
    }
}

$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $query = "
        SELECT 
            buku.id_buku, 
            buku.judul_buku, 
            buku.pengarang, 
            buku.penerbit, 
            buku.tahun_terbit, 
            kategori.nama_kategori, 
            buku.status 
        FROM buku
        LEFT JOIN kategori ON buku.id_kategori = kategori.id_kategori
        WHERE buku.judul_buku LIKE '%$search%'
    ";
} elseif (isset($_GET['category'])) {
    $category_id = $_GET['category'];
    $query_kategori = "SELECT nama_kategori FROM kategori WHERE id_kategori = '$category_id'";
    $result_kategori = mysqli_query($conn, $query_kategori);

    if ($result_kategori && mysqli_num_rows($result_kategori) > 0) {
        $kategori = mysqli_fetch_assoc($result_kategori)['nama_kategori'];
        $query = "
            SELECT 
                buku.id_buku, 
                buku.judul_buku, 
                buku.pengarang, 
                buku.penerbit, 
                buku.tahun_terbit, 
                kategori.nama_kategori, 
                buku.status 
            FROM buku
            LEFT JOIN kategori ON buku.id_kategori = kategori.id_kategori
            WHERE kategori.id_kategori = '$category_id'
        ";
    } else {
        $query = "
            SELECT 
                buku.id_buku, 
                buku.judul_buku, 
                buku.pengarang, 
                buku.penerbit, 
                buku.tahun_terbit, 
                kategori.nama_kategori, 
                buku.status 
            FROM buku
            LEFT JOIN kategori ON buku.id_kategori = kategori.id_kategori
        ";
    }
} else {
    $query = "
        SELECT 
            buku.id_buku, 
            buku.judul_buku, 
            buku.pengarang, 
            buku.penerbit, 
            buku.tahun_terbit, 
            kategori.nama_kategori, 
            buku.status 
        FROM buku
        LEFT JOIN kategori ON buku.id_kategori = kategori.id_kategori
    ";
}

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library</title>
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
        .card-body .btn-group {
            margin-top: 10px;
        }

        .card-body .btn {
            margin-right: 10px;
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
          <a class="nav-link dropdown-toggle active" href="#" id="categoryDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
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
        <?php if ($notification) { ?>
        <div class="alert alert-info"><?php echo $notification; ?></div>
        <?php } ?>

        <?php
        if (isset($_GET['category'])) {
            $category_id = $_GET['category'];
            $query_kategori = "SELECT nama_kategori FROM kategori WHERE id_kategori = '$category_id'";
            $result_kategori = mysqli_query($conn, $query_kategori);

            if ($result_kategori && mysqli_num_rows($result_kategori) > 0) {
                $kategori = mysqli_fetch_assoc($result_kategori)['nama_kategori'];
                echo "<h1>Books - Category: $kategori</h1>";
            } else {
                echo "<h1>Books - Category Not Found</h1>";
            }
        } else {
            echo "<h1>Books</h1>";
        }
        ?>

        <div class="row">
            <?php while ($book = mysqli_fetch_assoc($result)) { ?>
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $book['judul_buku']; ?></h5>
                        <h6 class="card-subtitle mb-2 text-muted">Author: <?php echo $book['pengarang']; ?></h6>
                        <p class="card-text">Publisher: <?php echo $book['penerbit']; ?></p>
                        <p class="card-text">Year: <?php echo $book['tahun_terbit']; ?></p>
                        <form method="POST" action="">
                            <input type="hidden" name="id_buku" value="<?php echo $book['id_buku']; ?>">
                            <div class="btn-group">
                                <?php if ($book['status'] == 0) { ?>
                                <button type="submit" class="btn btn-primary" name="borrow_book">Borrow</button>
                                <?php } else { ?>
                                <button type="button" class="btn btn-success" disabled>Borrowed</button>
                                <?php } ?>
                                <?php 
                                    // Check if book is already liked by user
                                    $query_check_favorite = "SELECT * FROM favorite_buku WHERE id_buku = '{$book['id_buku']}' AND id_user = '{$user_data['id_user']}'";
                                    $result_check_favorite = mysqli_query($conn, $query_check_favorite);
                                    if (mysqli_num_rows($result_check_favorite) > 0) {
                                        // Book is liked, show Unlike button
                                        echo '<button type="submit" class="btn btn-outline-primary" name="like_book">Unlike</button>';
                                    } else {
                                        // Book is not liked, show Like button
                                        echo '<button type="submit" class="btn btn-outline-primary" name="like_book">Like</button>';
                                    }
                                ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>

    <footer class="bg-light text-center text-lg-start mt-5">
        <div class="container p-4">
            <div class="row">
                <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                    <h5 class="text-uppercase">Digilib</h5>
                    <p>This is a library management system. You can manage books, categories, and users through this
                        admin panel.</p>
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
