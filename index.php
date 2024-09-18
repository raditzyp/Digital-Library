<?php
session_start();
include("includes/config.php");
include("includes/functions.php");

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Library Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Welcome to Digital Library</h1>
        <h2>Dashboard</h2>
        <div class="stats">
            <div class="stat-item">
                <h3>Total Books</h3>
                <p><?php echo $total_books; ?></p>
            </div>
            <div class="stat-item">
                <h3>Total Users</h3>
                <p><?php echo $total_users; ?></p>
            </div>
            <div class="stat-item">
                <h3>Total Borrowings</h3>
                <p><?php echo $total_borrowings; ?></p>
            </div>
        </div>
        <div class="menu">
            <?php if (isset($_SESSION['admin_id'])) { ?>
                <h3>Admin Menu</h3>
                <ul>
                    <li><a href="admin/manage_books.php">Manage Books</a></li>
                    <li><a href="admin/manage_categories.php">Manage Categories</a></li>
                    <li><a href="admin/manage_users.php">Manage Users</a></li>
                    <li><a href="admin/transactions.php">View Transactions</a></li>
                </ul>
            <?php } else { ?>
                <h3>User Menu</h3>
                <ul>
                    <li><a href="user/borrow_book.php">Borrow Book</a></li>
                    <li><a href="user/return_book.php">Return Book</a></li>
                    <li><a href="user/read_book.php">Read Book</a></li>
                </ul>
            <?php } ?>
        </div>
        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>
    </div>
</body>
</html>
