<?php
session_start();
include("../includes/config.php");
include("../includes/functions.php");

check_admin_login($conn);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_confirm'])) {
        $id_user = $_POST['id_user'];

        $query = "DELETE FROM users WHERE id_user = '$id_user'";
        mysqli_query($conn, $query);
    }
}

$query = "SELECT * FROM users";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var idUser = button.getAttribute('data-id');
            var modalBodyInput = document.getElementById('delete_id_user');
            modalBodyInput.value = idUser;
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
            <li><a class="dropdown-item" href="manage_books.php">Book</a></li>
            <li><a class="dropdown-item" href="manage_categories.php">Category</a></li>
            <li><a class="dropdown-item active" href="manage_users.php">User</a></li>
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
    <h1>Manage Users</h1>
    <h2 class="mt-4">User List</h2>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-primary">
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo $row['id_user']; ?>">
                                Delete
                            </button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this user?
            </div>
            <div class="modal-footer">
                <form method="POST">
                    <input type="hidden" name="id_user" id="delete_id_user">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="delete_confirm" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
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
