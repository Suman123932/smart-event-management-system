<?php
session_start();
$conn = mysqli_connect("localhost","root","","event_db");

$email = $_SESSION['reset_email'];
$new_password = $_POST['new_password'];

mysqli_query($conn, "UPDATE users SET password='$new_password', otp=NULL, otp_expire=NULL WHERE email='$email'");

session_destroy();

echo "Password Updated Successfully!";
?>