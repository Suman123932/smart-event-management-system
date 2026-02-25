<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['role'] != "admin") {
    header("Location: login.html");
    exit();
}
?>

<h2>Admin Dashboard</h2>
<p>Welcome <?php echo $_SESSION['user']; ?></p>
<a href="logout.php">Logout</a>