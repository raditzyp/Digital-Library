<?php
session_start();
include("../includes/config.php");
include("../includes/functions.php");

$user_data = check_login($conn);
$id_user = $user_data['id_user'];

// Get search term from GET request
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : '';

// Handle Unlike action
if (isset($_POST['unlike_book'])) {
    $id_buku = $_POST['id_buku'];
    $query_unlike = "DELETE FROM favorite_buku WHERE id_buku = '$id_buku' AND id_user = '$id_user'";
    $result_unlike = mysqli_query($conn, $query_unlike);
    if ($result_unlike) {
        header("Location: favorite_book.php"); // Redirect back to favorite books page
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Handle Borrow action
if (isset($_POST['borrow_book'])) {
    $id_buku = $_POST['id_buku'];

    // Insert into pinjam table
    $query_borrow = "INSERT INTO pinjam (id_user, id_buku) VALUES ('$id_user', '$id_buku')";
    $result_borrow = mysqli_query($conn, $query_borrow);

    // Update status to 'dipinjam' in buku table
    if ($result_borrow) {
        $query_update_status = "UPDATE buku SET status = 1 WHERE id_buku = '$id_buku'";
        $result_update_status = mysqli_query($conn, $query_update_status);
        if ($result_update_status) {
            header("Location: favorite_book.php"); // Redirect back to favorite books page
            exit();
        } else {
            echo "Error updating book status: " . mysqli_error($conn);
        }
    } else {
        echo "Error borrowing book: " . mysqli_error($conn);
    }
}

// Fetch favorite books
$query = "SELECT f.*, b.judul_buku, b.pengarang, b.penerbit, b.tahun_terbit, b.status FROM favorite_buku f 
          JOIN buku b ON f.id_buku = b.id_buku 
          WHERE f.id_user = '$id_user'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorite Books</title>
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
        .btn-group > .btn {
            margin-right: 10px; /* Adjust margin between buttons */
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
            <li><a class="dropdown-item active" href="favorite_book.php">Favorite Book</a></li>
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
    <h1 class="text-center">Favorite Books</h1>
    <div class="row row-cols-1 row-cols-md-3 g-4">
    <?php
      if(mysqli_num_rows($result) > 0){
          while($row = mysqli_fetch_assoc($result)){
              ?>
              <div class="col">
                  <div class="card h-100">
                      <div class="card-body">
                          <h5 class="card-title"><?php echo $row['judul_buku']; ?></h5>
                          <h6 class="card-subtitle mb-2 text-muted">Author: <?php echo $row['pengarang']; ?></h6>
                          <p class="card-text">Publisher: <?php echo $row['penerbit']; ?></p>
                          <p class="card-text">Year: <?php echo $row['tahun_terbit']; ?></p>
                          <form method="POST" action="">
                              <input type="hidden" name="id_buku" value="<?php echo $row['id_buku']; ?>">
                              <div class="btn-group" role="group">
                                  <?php if ($row['status'] == 0) { ?>
                                      <button type="submit" class="btn btn-primary me-2" name="borrow_book">Borrow</button>
                                  <?php } else { ?>
                                      <button type="button" class="btn btn-success me-2" disabled>Read</button>
                                  <?php } ?>
                                  <?php 
                                  // Check if book is already liked by user
                                  $query_check_favorite = "SELECT * FROM favorite_buku WHERE id_buku = '{$row['id_buku']}' AND id_user = '{$id_user}'";
                                  $result_check_favorite = mysqli_query($conn, $query_check_favorite);
                                  if (mysqli_num_rows($result_check_favorite) > 0) {
                                      // Book is liked, show Unlike button
                                      echo '<button type="submit" class="btn btn-outline-primary" name="unlike_book">Unlike</button>';
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
        <?php } else { ?>
            <div class="col-12">
                <div class="alert alert-secondary text-center">
                    You have not favorite any books yet
                </div>
            </div>
        <?php } ?>
    </div>
</div>
</body>
</html>
