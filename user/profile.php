<?php
session_start();
include("../includes/config.php");
include("../includes/functions.php");

$user_data = check_login($conn);

// Inisialisasi variabel
$name = $user_data['name'];
$email = $user_data['email'];
$notification = '';

// Proses update profil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $id_user = $user_data['id_user'];

    $query = "UPDATE users SET name = '$name', email = '$email' WHERE id_user = '$id_user'";
    if (mysqli_query($conn, $query)) {
        $notification = "Profil berhasil diperbarui.";
        // Update session data
        $_SESSION['user'] = ['id_user' => $id_user, 'name' => $name, 'email' => $email];
    } else {
        $notification = "Terjadi kesalahan saat memperbarui profil.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Profile</title>
</head>
<body>
    <h1>Manage Profile</h1>
    <?php if ($notification != '') { ?>
        <p><?php echo $notification; ?></p>
    <?php } ?>
    <form method="POST">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" value="<?php echo $name; ?>"><br>
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" value="<?php echo $email; ?>"><br><br>
        <input type="submit" value="Update Profile">
    </form>
</body>
</html>
