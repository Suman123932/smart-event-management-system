<?php
session_start();
$conn = mysqli_connect("localhost","root","","event_db");

$email = $_SESSION['reset_email'];
$otp = $_POST['otp'];

$result = mysqli_query($conn, "SELECT * FROM users 
WHERE email='$email' AND otp='$otp' 
AND otp_expire > NOW()");

if(mysqli_num_rows($result) > 0){
    header("Location: reset_password.html");
} else {
    echo "Invalid or Expired OTP!";
}
?>