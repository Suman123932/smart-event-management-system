<?php
session_start();
$conn = new mysqli("localhost", "root", "", "eventdb");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_SESSION['reset_email'];
    $otp = $_POST['otp'];

    $stmt = $conn->prepare("SELECT otp, otp_expiry FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($db_otp, $db_expiry);
    $stmt->fetch();

    if ($otp == $db_otp && strtotime($db_expiry) > time()) {
        header("Location: reset_password.html");
        exit();
    } else {
        echo "Invalid or Expired OTP";
    }
}
?>