<?php
session_start();
include("../includes/config.php");
include("../includes/functions.php");

check_admin_login($conn);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $judul_buku = $_POST['judul_buku'];
        $pengarang = $_POST['pengarang'];
        $penerbit = $_POST['penerbit'];
        $tahun_terbit = $_POST['tahun_terbit'];
        $id_kategori = $_POST['id_kategori'];

        // Handle file upload
        $file_pdf = $_FILES['file_pdf']['name'];
        $file_temp = $_FILES['file_pdf']['tmp_name'];
        $file_size = $_FILES['file_pdf']['size'];
        $file_error = $_FILES['file_pdf']['error'];

        if ($file_error === 0 && $file_size > 0) {
            $file_ext = pathinfo($file_pdf, PATHINFO_EXTENSION);
            $file_name = $_FILES['file_pdf']['name'];
            $file_destination = "../uploads/" . $file_name;
            move_uploaded_file($file_temp, $file_destination);
        } else {
            $file_name = null;
        }

        $query = "INSERT INTO buku (judul_buku, pengarang, penerbit, tahun_terbit, id_kategori, file_pdf, status) 
        VALUES ('$judul_buku', '$pengarang', '$penerbit', '$tahun_terbit', '$id_kategori', '$file_name', 0)";
        mysqli_query($conn, $query);
    } elseif (isset($_POST['delete'])) {
        $id_buku = $_POST['id_buku'];

        // Delete the associated PDF file
        $query = "SELECT file_pdf FROM buku WHERE id_buku = '$id_buku'";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);
        if ($row['file_pdf']) {
            unlink("../uploads/" . $row['file_pdf']);
        }

        $query = "DELETE FROM buku WHERE id_buku = '$id_buku'";
        mysqli_query($conn, $query);
    } elseif (isset($_POST['edit'])) {
        $id_buku = $_POST['id_buku'];
        $judul_buku = $_POST['judul_buku'];
        $pengarang = $_POST['pengarang'];
        $penerbit = $_POST['penerbit'];
        $tahun_terbit = $_POST['tahun_terbit'];
        $id_kategori = $_POST['id_kategori'];

        // Handle file upload
        if (isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] === 0) {
            $file_pdf = $_FILES['file_pdf']['name'];
            $file_temp = $_FILES['file_pdf']['tmp_name'];
            $file_size = $_FILES['file_pdf']['size'];
            $file_error = $_FILES['file_pdf']['error'];

            if ($file_error === 0 && $file_size > 0) {
                $file_ext = pathinfo($file_pdf, PATHINFO_EXTENSION);
                $file_name = $_FILES['file_pdf']['name'];
                $file_destination = "../uploads/" . $file_name;
                move_uploaded_file($file_temp, $file_destination);

                // Delete the old PDF file
                $query = "SELECT file_pdf FROM buku WHERE id_buku = '$id_buku'";
                $result = mysqli_query($conn, $query);
                $row = mysqli_fetch_assoc($result);
                if ($row['file_pdf']) {
                    unlink("../uploads/" . $row['file_pdf']);
                }

                // Update the record with the new file
                $query = "UPDATE buku SET judul_buku = '$judul_buku', pengarang = '$pengarang', penerbit = '$penerbit', tahun_terbit = '$tahun_terbit', id_kategori = '$id_kategori', file_pdf = '$file_name' WHERE id_buku = '$id_buku'";
            } else {
                $query = "UPDATE buku SET judul_buku = '$judul_buku', pengarang = '$pengarang', penerbit = '$penerbit', tahun_terbit = '$tahun_terbit', id_kategori = '$id_kategori' WHERE id_buku = '$id_buku'";
            }
        } else {
            $query = "UPDATE buku SET judul_buku = '$judul_buku', pengarang = '$pengarang', penerbit = '$penerbit', tahun_terbit = '$tahun_terbit', id_kategori = '$id_kategori' WHERE id_buku = '$id_buku'";
        }

        mysqli_query($conn, $query);
    }
}

// Handle book search
$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT buku.*, kategori.nama_kategori FROM buku JOIN kategori ON buku.id_kategori = kategori.id_kategori";
if ($search) {
    $search = mysqli_real_escape_string($conn, $search);
    $query .= " WHERE buku.judul_buku LIKE '%$search%'";
}
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript to set the book data in the edit modal form
        document.addEventListener('DOMContentLoaded', function () {
            var editButtons = document.querySelectorAll('.btn-edit');
            editButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    var bookId = this.getAttribute('data-id');
                    var bookTitle = this.getAttribute('data-title');
                    var bookAuthor = this.getAttribute('data-author');
                    var bookPublisher = this.getAttribute('data-publisher');
                    var bookYear = this.getAttribute('data-year');
                    var bookCategory = this.getAttribute('data-category');

                    document.getElementById('edit_id_buku').value = bookId;
                    document.getElementById('edit_judul_buku').value = bookTitle;
                    document.getElementById('edit_pengarang').value = bookAuthor;
                    document.getElementById('edit_penerbit').value = bookPublisher;
                    document.getElementById('edit_tahun_terbit').value = bookYear;
                    document.getElementById('edit_id_kategori').value = bookCategory;
                });
            });

            // JavaScript to set the book data in the delete modal form
            var deleteButtons = document.querySelectorAll('.btn-delete');
            deleteButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var bookId = this.getAttribute('data-id');
                document.getElementById('delete_id_buku').value = bookId;
            });
        });
        });
    </script>
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
        .content {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .main {
            flex: 1;
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
                    <a class="nav-link" aria-current="page" href="index.php">Home</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link active dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Manage
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item active" href="manage_books.php">Book</a></li>
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
    <h1 class="mb-4">Manage Books</h1>

    <!-- Form Tambah Buku -->
    <form method="POST" enctype="multipart/form-data" class="border p-4 bg-light">
        <div class="form-group">
            <label for="judul_buku">Title:</label>
            <input type="text" class="form-control" id="judul_buku" name="judul_buku" required>
        </div>
        <div class="form-group">
            <label for="pengarang">Author:</label>
            <input type="text" class="form-control" id="pengarang" name="pengarang" required>
        </div>
        <div class="form-group">
            <label for="penerbit">Publisher:</label>
            <input type="text" class="form-control" id="penerbit" name="penerbit" required>
        </div>
        <div class="form-group">
            <label for="tahun_terbit">Publication Year:</label>
            <input type="number" class="form-control" id="tahun_terbit" name="tahun_terbit" required>
        </div>
        <div class="form-group">
            <label for="id_kategori">Category:</label>
            <select class="form-control" id="id_kategori" name="id_kategori" required>
                <?php
                $query = "SELECT * FROM kategori";
                $categories = mysqli_query($conn, $query);
                while ($category = mysqli_fetch_assoc($categories)) {
                    echo "<option value='" . $category['id_kategori'] . "'>" . $category['nama_kategori'] . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="file_pdf">File PDF:</label>
            <input type="file" class="form-control" id="file_pdf" name="file_pdf">
        </div>
        <button type="submit" name="add" class="btn btn-primary mt-3">Add Book</button>
    </form>

    <h2 class="mt-5">Book List</h2>
        <!-- Form Pencarian Buku -->
        <!-- <form method="GET" action="manage_books.php" class="mb-4">
        <div class="input-group">
            <input type="text" class="form-control" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Enter book title...">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form> -->
    <div class="container-fluid bg-body-tertiary mb-4">
    <form class="d-flex" method="GET" action="manage_books.php" role="search">
      <input class="form-control me-2" type="search" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Enter book title..." aria-label="Search">
      <button class="btn btn-outline-success" type="submit">Search</button>
    </form>
    </div>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Title</th>
                <th>Author</th>
                <th>Publisher</th>
                <th>Publication Year</th>
                <th>Category</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $row['id_buku']; ?></td>
                    <td><?php echo $row['judul_buku']; ?></td>
                    <td><?php echo $row['pengarang']; ?></td>
                    <td><?php echo $row['penerbit']; ?></td>
                    <td><?php echo $row['tahun_terbit']; ?></td>
                    <td><?php echo $row['nama_kategori']; ?></td>
                    <td><?php echo $row['status'] == 0 ? 'Available' : 'Not Available'; ?></td>
                    <td>
                        <button type="button" class="btn btn-warning btn-edit" data-bs-toggle="modal" data-bs-target="#editModal"
                            data-id="<?php echo $row['id_buku']; ?>"
                            data-title="<?php echo $row['judul_buku']; ?>"
                            data-author="<?php echo $row['pengarang']; ?>"
                            data-publisher="<?php echo $row['penerbit']; ?>"
                            data-year="<?php echo $row['tahun_terbit']; ?>"
                            data-category="<?php echo $row['id_kategori']; ?>">Edit</button>
                        <button type="button" class="btn btn-danger btn-delete" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo $row['id_buku']; ?>">Delete</button>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
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

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_id_buku" name="id_buku">
                    <div class="form-group">
                        <label for="edit_judul_buku">Title:</label>
                        <input type="text" class="form-control" id="edit_judul_buku" name="judul_buku" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_pengarang">Author:</label>
                        <input type="text" class="form-control" id="edit_pengarang" name="pengarang" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_penerbit">Publisher:</label>
                        <input type="text" class="form-control" id="edit_penerbit" name="penerbit" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_tahun_terbit">Publication Year:</label>
                        <input type="number" class="form-control" id="edit_tahun_terbit" name="tahun_terbit" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_id_kategori">Category:</label>
                        <select class="form-control" id="edit_id_kategori" name="id_kategori" required>
                            <?php
                            $query = "SELECT * FROM kategori";
                            $categories = mysqli_query($conn, $query);
                            while ($category = mysqli_fetch_assoc($categories)) {
                                echo "<option value='" . $category['id_kategori'] . "'>" . $category['nama_kategori'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_file_pdf">File PDF:</label>
                        <input type="file" class="form-control" id="edit_file_pdf" name="file_pdf">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="edit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="delete_id_buku" name="id_buku">
                    <p>Are you sure you want to delete this book?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
