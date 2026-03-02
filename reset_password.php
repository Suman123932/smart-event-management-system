<?php
session_start();
$conn = mysqli_connect("localhost","root","","eventdb");

$email = $_SESSION['reset_email'];
$new_password = password_hash($_POST['new_password'],PASSWORD_DEFAULT);

mysqli_query($conn, "UPDATE users SET password='$new_password', otp=NULL, otp_expire=NULL WHERE email='$email'");

session_destroy();

echo "Password Updated Successfully!";
?>
<html>
    <br>
    <a href="login.html">Go back to Login Page</a>
    </html>