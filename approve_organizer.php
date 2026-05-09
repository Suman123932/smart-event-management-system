<?php
session_start();

/* ROLE CHECK */

if (
    !isset($_SESSION['role']) ||
    $_SESSION['role'] != 'event_organizer'
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

/* UPDATE STATUS */

$updateQuery = "
UPDATE events
SET organizer_status='Approved'
WHERE id='$id'
";

mysqli_query($conn, $updateQuery);

/* FETCH EVENT DETAILS */

$eventQuery = "
SELECT *
FROM events
WHERE id='$id'
";

$eventResult = mysqli_query($conn, $eventQuery);

$eventData = mysqli_fetch_assoc($eventResult);

$title = $eventData['title'];
$department = $eventData['department'];
$venue = $eventData['venue'];
$date = $eventData['event_date'];

/* FETCH SUPER ADMIN EMAIL */

$adminQuery = "
SELECT email
FROM users
WHERE role='super_admin'
LIMIT 1
";

$adminResult = mysqli_query($conn, $adminQuery);

$adminData = mysqli_fetch_assoc($adminResult);

$superAdminEmail = $adminData['email'];

/* SEND EMAIL TO SUPER ADMIN */

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
        'sahoosuman4500@gmail.com',
        'Event Management System'
    );

    $mail->addAddress($superAdminEmail);

    $mail->isHTML(true);

    $mail->Subject = 'Event Approved By Organizer';

    $mail->Body = "
    <h2>Organizer Approved Event</h2>

    <p><b>Department:</b> $department</p>

    <p><b>Event Title:</b> $title</p>

    <p><b>Venue:</b> $venue</p>

    <p><b>Event Date:</b> $date</p>

    <p>
    Please login to Super Admin Dashboard
    for final approval.
    </p>
    ";

    $mail->send();

}
catch (Exception $e) {

    echo 'Mailer Error: ' . $mail->ErrorInfo;

}

/* REDIRECT */

header("Location: organiser_dashboard.php");

exit();

?>