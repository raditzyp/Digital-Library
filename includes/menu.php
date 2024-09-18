<?php
session_start();
?>

<nav>
    <ul>
        <?php if (isset($_SESSION['user_id'])) { ?>
            <!-- User menu -->
            <li><a href="user/borrow_book.php">Borrow Book</a></li>
            <li><a href="user/return_book.php">Return Book</a></li>
            <li><a href="user/read_book.php">Read Book</a></li>
            <li><a href="logout.php">Logout</a></li>
        <?php } elseif (isset($_SESSION['admin_id'])) { ?>
            <!-- Admin menu -->
            <li><a href="admin/manage_books.php">Manage Books</a></li>
            <li><a href="admin/manage_categories.php">Manage Categories</a></li>
            <li><a href="admin/manage_users.php">Manage Users</a></li>
            <li><a href="admin/transactions.php">Transactions</a></li>
            <li><a href="logout.php">Logout</a></li>
        <?php } else { ?>
            <!-- Guest menu -->
            <li><a href="/login.php">Login</a></li>
            <li><a href="/register.php">Register</a></li>
        <?php } ?>
    </ul>
</nav>
