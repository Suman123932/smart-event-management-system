<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
session_start();
if (!isset($_SESSION['user']) || $_SESSION['role'] != "event_organizer") {
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Event Organizer Dashboard</title>
</head>
<body>
    <h1>Welcome,<?php echo $_SESSION['user']; ?></h1>
    <p>Your role:<?php echo $_SESSION['role']; ?></p>
<a href="logout.php">Logout</a>
</body>
</html>