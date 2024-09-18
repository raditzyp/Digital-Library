<?php
session_start();
include("../includes/config.php");
include("../includes/functions.php");

$user_data = check_login($conn);
$id_user = $user_data['id_user'];

// Get search term from GET request
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : '';

// Process return book request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_buku'])) {
    $id_buku = $_POST['id_buku'];
    
    // Retrieve borrowing details before deletion
    $select_query = "SELECT * FROM pinjam WHERE id_buku = '$id_buku' AND id_user = '$id_user'";
    $result = mysqli_query($conn, $select_query);
    $borrow_data = mysqli_fetch_assoc($result);
    
    // Delete borrowing entry
    $delete_query = "DELETE FROM pinjam WHERE id_buku = '$id_buku' AND id_user = '$id_user'";
    if (mysqli_query($conn, $delete_query)) {
        // Insert into transaction history
        $id_pinjam = $borrow_data['id_pinjam'];
        $tanggal_peminjaman = $borrow_data['tanggal_peminjaman'];
        $tanggal_pengembalian = $borrow_data['tanggal_pengembalian'];
        
        $insert_query = "INSERT INTO transaksi (id_pinjam, id_user, id_buku, tanggal_peminjaman, tanggal_pengembalian)
                         VALUES ('$id_pinjam', '$id_user', '$id_buku', '$tanggal_peminjaman', '$tanggal_pengembalian')";
        
        if (mysqli_query($conn, $insert_query)) {
            // Update book status to available if no one else has borrowed it
            $update_status_query = "UPDATE buku 
                                   SET status = 0 
                                   WHERE id_buku = '$id_buku' 
                                   AND NOT EXISTS (
                                       SELECT * FROM pinjam 
                                       WHERE id_buku = '$id_buku'
                                   )";
            mysqli_query($conn, $update_status_query);
            
            // Send JSON response for AJAX handling
            echo json_encode(array('status' => 'success'));
            exit;
        } else {
            echo json_encode(array('status' => 'error'));
            exit;
        }
    } else {
        echo json_encode(array('status' => 'error'));
        exit;
    }
}

// Query to retrieve books that the logged-in user has borrowed, including the return date
$query = "
SELECT buku.id_buku, buku.judul_buku, buku.pengarang, buku.file_pdf, pinjam.tanggal_pengembalian
FROM buku
JOIN pinjam ON buku.id_buku = pinjam.id_buku
WHERE pinjam.id_user = '$id_user' AND buku.status = 1";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reading List</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            <li><a class="dropdown-item active" href="read_book.php">Reading List</a></li>
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
    <h1 class="text-center">Reading List</h1>
    <div class="row">
        <?php if (mysqli_num_rows($result) > 0) { ?>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title "><?php echo htmlspecialchars($row['judul_buku'], ENT_QUOTES, 'UTF-8'); ?></h5>
                            <p class="card-text">By <?php echo htmlspecialchars($row['pengarang'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <p class="card-text">Due Date: <?php echo htmlspecialchars($row['tanggal_pengembalian'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#readBookModal<?php echo $row['id_buku']; ?>">
                                Read
                            </button>
                            <button type="button" class="btn btn-danger return-book-btn" data-id="<?php echo $row['id_buku']; ?>" data-bs-toggle="modal" data-bs-target="#returnBookModal">Return</button>
                        </div>
                    </div>
                </div>
                <!-- Modal -->
                <div class="modal fade" id="readBookModal<?php echo $row['id_buku']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel"><?php echo htmlspecialchars($row['judul_buku'], ENT_QUOTES, 'UTF-8'); ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <?php
                                $pdf_path = "../uploads/" . htmlspecialchars($row['file_pdf'], ENT_QUOTES, 'UTF-8');
                                if (file_exists($pdf_path)) {
                                    echo "<iframe src='$pdf_path' width='100%' height='600px'></iframe>";
                                } else {
                                    echo "<div class='alert alert-danger'>The book content is not available online. File not found: $pdf_path</div>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="col-12">
                <div class="alert alert-secondary text-center">
                    You have not borrowed any books yet
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<!-- Return Book Modal -->
<div class="modal fade" id="returnBookModal" tabindex="-1" aria-labelledby="returnBookModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="returnBookModalLabel">Return Book</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to return this book?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-danger" id="confirmReturnBtn">Return</button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    var bookId;
    
    $('.return-book-btn').on('click', function() {
        bookId = $(this).data('id');
    });

    $('#confirmReturnBtn').on('click', function() {
        $.ajax({
            url: '',  // Adjust the URL if needed
            type: 'POST',
            data: { id_buku: bookId },
            success: function(response) {
                var res = JSON.parse(response);
                if (res.status == 'success') {
                    $('#returnBookModal').modal('hide');
                    alert('Book returned successfully!');
                    location.reload(); // Reload the page to update the book list
                } else {
                    alert('Failed to return the book. Please try again.');
                }
            },
            error: function() {
                alert('Failed to return the book. Please try again.');
            }
        });
    });
});
</script>
</body>
</html>
