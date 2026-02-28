<?php
session_start();
$conn = mysqli_connect("localhost","root","","eventdb");

$email = $_POST['email'];

$result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

if(mysqli_num_rows($result) > 0){

    $otp = rand(100000,999999);
    $expire = date("Y-m-d H:i:s", strtotime("+5 minutes"));

    mysqli_query($conn, "UPDATE users SET otp='$otp', otp_expire='$expire' WHERE email='$email'");

    $_SESSION['reset_email'] = $email;

    // ====== MAIL SENDING ======
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';
    require 'PHPMailer/src/Exception.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer();

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'sahoosuman4500@gmail.com';
    $mail->Password = 'qaruexzfblwjguwm';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('sahoosuman4500@gmail.com', 'Event System');
    $mail->addAddress($email);

    $mail->Subject = 'Password Reset OTP';
    $mail->Body = "Your OTP is: $otp";

    if($mail->send()){
        header("Location: verify_otp.html");
    } else {
        echo "Email sending failed";
    }

} else {
    echo "Email not registered!";
}
?>