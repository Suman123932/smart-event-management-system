<?php
session_start();
date_default_timezone_set('Asia/Kolkata');
echo "Session Email:".$_SESSION['reset_email'];
//echo"<br> Entered OTP:".$_POST['otp'];
$conn = mysqli_connect("localhost","root","","eventdb");

$email = $_SESSION['reset_email'];
$otp = trim($_POST['otp']);

$result = mysqli_query($conn, "SELECT otp FROM users 
WHERE email='$email' AND otp='$otp' 
AND STR_TO_DATE(otp_expire,'%Y-%m-%d %H:%i:%s') > NOW()");
//echo "<br>Database OTP:".$result['otp'];
if(mysqli_num_rows($result) > 0){
    header("Location: reset_password.html");
} else {
    echo "Invalid or Expired OTP!";
}
?>