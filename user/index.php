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
    $tanggal_pengembalian = date('Y-m-d', strtotime($tanggal_peminjaman . ' + 30 days'));

    // Check if the book is already borrowed
    $query_check = "SELECT status FROM buku WHERE id_buku = '$id_buku'";
    $result_check = mysqli_query($conn, $query_check);
    $book_status = mysqli_fetch_assoc($result_check);

    if ($book_status['status'] == 1) {
        $notification = "Buku ini sedang dipinjam oleh pengguna lain.";
    } else {
        // Ambil judul buku dari database berdasarkan id_buku
        $query_buku = "SELECT judul_buku FROM buku WHERE id_buku = '$id_buku'";
        $result_buku = mysqli_query($conn, $query_buku);
        if ($row_buku = mysqli_fetch_assoc($result_buku)) {
            $judul_buku = $row_buku['judul_buku'];
        } else {
            $judul_buku = "Unknown";
        }

        // Insert into pinjam table
        $query_insert_pinjam = "INSERT INTO pinjam (id_buku, id_user, tanggal_peminjaman, tanggal_pengembalian) 
                                VALUES ('$id_buku', '$id_user', '$tanggal_peminjaman', '$tanggal_pengembalian')";
        // Update the book status
        $query_update_buku = "UPDATE buku SET status = 1 WHERE id_buku = '$id_buku'";

        if (mysqli_query($conn, $query_insert_pinjam) && mysqli_query($conn, $query_update_buku)) {
            $notification = "Buku: $judul_buku berhasil dipinjam. Tanggal peminjaman: $tanggal_peminjaman, Tanggal pengembalian: $tanggal_pengembalian.";
        } else {
            $notification = "Terjadi kesalahan saat meminjam buku.";
        }
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

        .card-body .btn-group {
            margin-top: 10px;
        }
        .card-body .btn {
            margin-right: 10px;
        }
        .books-section {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #ddd;
            position: relative;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
            margin-bottom: 40px; /* Added margin-bottom */
        }
        .card {
            background-color: rgba(255, 255, 255, 0.9);
        }

        .books-section .d-flex {
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: fit-content;
        }

        .username {
            color: #0d6efd;
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

        .section-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 40px;
            min-width: fit-content;
            gap: 20px; /* Added gap between sections */
        }

        .section {
            width: 43%; /* Adjusted width to fit three sections in a row */
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            border: 1px solid #ddd;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
        }

        .section h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .section p {
            text-align: center; /* Center align the description */
            margin-bottom: 20px; /* Added margin for spacing */
        }

        .section a.btn {
            margin-bottom: -50px;
            left: 50%;
            transform: translateX(100%);
            width: fit-content;
        }

        .section a.btnn {
            margin-bottom: -50px;
            left: 50%;
            transform: translateX(90%);
            width: fit-content;
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
          <a class="nav-link active" aria-current="page" href="index.php">Home</a>
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


<div class="container p-5"></div>
    <div class="d-flex justify-content-between align-items-center my-5 py-5 px-5">
        <div class="col p-5 mx-auto">
            <h2 class="display-8 fw-bold" style="margin-left: 15%; font-size: 40px;">
              Welcome to Digital Library, 
              <p class="username">
                <?php echo htmlspecialchars($user_data['name'], ENT_QUOTES, 'UTF-8'); ?>
              </p>
            </h2>
            <button style="margin-left: 15%;"  type="button" class="btn btn-primary btn-lg mt-4" onclick="scrollToBooks()">Get Started</button>
        </div>
        <div class="col w-20 d-flex justify-content-center">
            <img src="../assets/images/jumbotron.png" alt="jumbotron" class="img-fluid" />
        </div>
    </div>
</div>

<br>

<div class="container p-5" style="margin-bottom: 100px;"></div>
<div id="books-section" class="container p-5 mt-5 books-section">
    <h1 style="text-align: center; margin-bottom:20px;">BOOKS</h1>
    <div class="row">
        <?php 
        $counter = 0; // Initialize counter
        while ($book = mysqli_fetch_assoc($result)) { 
            if ($counter >= 6) break; // Stop after 6 books
            $counter++;
        ?>
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
                                <button type="submit" class="btn btn-primary" name="borrow_book" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Borrow</button>
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
    <div class="d-flex justify-content-center mt-4">
        <a href="book.php" class="btn btn-primary">Browse More Books</a>
    </div>
</div>

<div class="section-wrapper">
    <div class="section">
        <h2>READING LIST</h2>
        <p>Explore and manage your reading list here.</p>
        <a href="read_book.php" class="btn btn-primary">Go to Reading List</a>
    </div>
    <div class="section">
        <h2>FAVORITE BOOK</h2>
        <p>Discover and keep track of your favorite books.</p>
        <a href="favorite_book.php" class="btn btnn btn-primary">Go to Favorite Books</a>
    </div>
</div>
<br><br><br><br>
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

<script>
    function scrollToBooks() {
        document.getElementById('books-section').scrollIntoView({ behavior: 'smooth' });
    }
</script>
</body>
</html>
