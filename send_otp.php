<?php
session_start();
include "db.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

date_default_timezone_set('Asia/Kolkata');

// Get email safely
if(isset($_POST['email'])){
    $email = $_POST['email'];
    $_SESSION['reset_email'] = $email;
} elseif(isset($_SESSION['reset_email'])){
    $email = $_SESSION['reset_email'];
} else {
    echo "Email not found!";
    exit();
}

// Check if email exists
$result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

if(mysqli_num_rows($result) > 0){

    $otp = rand(100000,999999);
    $expire = date("Y-m-d H:i:s", time() + 300);

    mysqli_query($conn, "UPDATE users SET otp='$otp', otp_expire='$expire' WHERE email='$email'");

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'sahoosuman4500@gmail.com';        // your gmail
        $mail->Password = 'qaruexzfblwjguwm'; // app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('sahoosuman4500@gmail.com', 'Event System');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Password Reset OTP';
        $mail->Body = "<h2>Your OTP is: $otp</h2><p>Valid for 5 minutes.</p>";

        if($mail->send()){
            header("Location:verify_otp.html");
            exit();
        }
        else{
        echo "Email sending failed";
        }

    } catch (Exception $e) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }

} else {
    echo "Email not registered!";
}

$conn->close();
?>