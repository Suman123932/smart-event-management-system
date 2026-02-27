<?php
session_start();
$conn = new mysqli("localhost", "root", "", "eventdb");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {

        $otp = rand(100000, 999999);
        $expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));

        $update = $conn->prepare("UPDATE users SET otp=?, otp_expiry=? WHERE email=?");
        $update->bind_param("sss", $otp, $expiry, $email);
        $update->execute();

        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'yourgmail@gmail.com';
        $mail->Password = 'lapdwmqiucsatttr';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('yourgmail@gmail.com', 'Event System');
        $mail->addAddress($email);

        $mail->Subject = 'Your OTP Code';
        $mail->Body = "Your OTP is: $otp (Valid for 5 minutes)";

        $mail->send();

        $_SESSION['reset_email'] = $email;
        header("Location: verify_otp.html");
        exit();

    } else {
        echo "Email not found";
    }
}
?>