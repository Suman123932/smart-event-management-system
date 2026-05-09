<?php
session_start();


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

/* FORM DATA */

$title = $_POST['title'];
$date = $_POST['date'];
$venue = $_POST['venue'];
$category = $_POST['category'];
$capacity = $_POST['capacity'];
$description = $_POST['description'];

/* SESSION DATA */

$user_id = $_SESSION['user_id'];
$department = $_SESSION['department'];
$deptAdminName = $_SESSION['user'];

/* INSERT EVENT */

$sql = "
INSERT INTO events
(
title,
event_date,
venue,
category,
capacity,
description,
created_by,
department,
organizer_status,
superadmin_status,
final_status,
created_at
)

VALUES
(
'$title',
'$date',
'$venue',
'$category',
'$capacity',
'$description',
'$department',
'$user_id',
'Pending',
'Pending',
'Pending',
NOW()
)
";

$result = mysqli_query($conn, $sql);

/* FETCH ORGANIZER EMAIL */

$organizerQuery = "
SELECT email
FROM users
WHERE role='event_organizer'
LIMIT 1
";

$organizerResult = mysqli_query($conn, $organizerQuery);

$organizerData = mysqli_fetch_assoc($organizerResult);

$organizerEmail = $organizerData['email'];

/* SEND EMAIL */

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

    $mail->setFrom('sahoosuman4500@gmail.com', 'Event Management System');

    $mail->addAddress($organizerEmail);

    $mail->isHTML(true);

    $mail->Subject = 'New Event Created';

    $mail->Body = "
    <h2>New Event Request</h2>

    <p><b>Department:</b> $department</p>

    <p><b>Created By:</b> $deptAdminName</p>

    <p><b>Event Title:</b> $title</p>

    <p><b>Venue:</b> $venue</p>

    <p><b>Category:</b> $category</p>

    <p><b>Event Date:</b> $date</p>

    <p>Please login to Organizer Dashboard to review this event.</p>
    ";

    $mail->send();

}
catch (Exception $e) {

    echo "Mailer Error: " . $mail->ErrorInfo;

}

/* REDIRECT */

header("Location: dept_admin_dashboard.php");

exit();

?>