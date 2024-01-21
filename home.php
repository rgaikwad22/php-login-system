<?php
session_start();

if (!isset($_SESSION['is_authenticated'])) {
    header('Location: login.php');
    exit();
}
?>

<h1>Home Page</h1>
<a href="<?php echo $_SESSION['user_role'] == "admin" ? "admin.php" : "user.php" ?>"><?php echo $_SESSION['user_role'] == "admin" ? "Admin" : "User"?></a>