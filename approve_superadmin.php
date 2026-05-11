<?php
session_start();

/* ROLE CHECK */

if (
    !isset($_SESSION['role']) ||
    $_SESSION['role'] != 'super_admin'
) {
    header("Location: login.html");
    exit();
}

/* DATABASE CONNECTION */

$conn = mysqli_connect("localhost", "root", "", "eventdb");

if (!$conn) {
    die("Database Connection Failed");
}

/* PHPMailer */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

/* EVENT ID */

$id = $_GET['id'];

/* FETCH EVENT + DEPT ADMIN DETAILS */

$query = "
SELECT events.*, users.email, users.full_name
FROM events
JOIN users
ON users.user_id = events.created_by
WHERE events.id='$id'
";

$result = mysqli_query($conn, $query);

$data = mysqli_fetch_assoc($result);

/* EVENT DETAILS */

$title = $data['title'];
$department = $data['department'];
$venue = $data['venue'];
$date = $data['event_date'];

$deptAdminEmail = $data['email'];
$deptAdminName = $data['name'];

/* UPDATE STATUS */

$updateQuery = "
UPDATE events
SET
superadmin_status='Approved',
final_status='Approved'
WHERE id='$id'
";

mysqli_query($conn, $updateQuery);

/* SEND EMAIL TO DEPT ADMIN */

$mail = new PHPMailer(true);

try {

    $mail->isSMTP();

    $mail->Host = 'smtp.gmail.com';

    $mail->SMTPAuth = true;

    /* YOUR GMAIL */

    $mail->Username = 'sahoosuman4500@gmail.com';

    /* YOUR APP PASSWORD */

    $mail->Password = 'qaruexzfblwjguwm';

    $mail->SMTPSecure = 'tls';

    $mail->Port = 587;

    $mail->setFrom(
        'sumansahoo4500@gmail.com',
        'Event Management System'
    );

    $mail->addAddress($deptAdminEmail);

    $mail->isHTML(true);

    $mail->Subject = 'Event Approved Successfully';

    $mail->Body = "
    <h2>Event Approved</h2>

    <p>Hello <b>$deptAdminName</b>,</p>

    <p>
    Your event has been successfully approved
    by the Super Admin.
    </p>

    <hr>

    <p><b>Department:</b> $department</p>

    <p><b>Event Title:</b> $title</p>

    <p><b>Venue:</b> $venue</p>

    <p><b>Event Date:</b> $date</p>

    <p>
    Final Status:
    <b style='color:green;'>Approved</b>
    </p>
    Students can now register using the link below:

    http://localhost/event/student_register.php

    Thank You.
    ";

    $mail->send();

}
catch (Exception $e) {

    echo 'Mailer Error: ' . $mail->ErrorInfo;

}

/* REDIRECT */

header("Location: http://localhost/event/super_admin_dashboard.php");
exit();

?>