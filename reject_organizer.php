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

/* FETCH EVENT + DEPT ADMIN DETAILS */

$query = "
SELECT events.*, users.email
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

/* UPDATE STATUS */

$updateQuery = "
UPDATE events
SET
organizer_status='Rejected',
final_status='Rejected'
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
        'sahoosuman4500@gmail.com',
        'Event Management System'
    );

    $mail->addAddress($deptAdminEmail);

    $mail->isHTML(true);

    $mail->Subject = 'Event Rejected By Organizer';

    $mail->Body = "
    <h2>Event Rejected</h2>

    <p>
    Your event request was rejected by
    the Event Organizer.
    </p>

    <hr>

    <p><b>Department:</b> $department</p>

    <p><b>Event Title:</b> $title</p>

    <p><b>Venue:</b> $venue</p>

    <p><b>Event Date:</b> $date</p>

    <p>Status: <b style='color:red;'>Rejected</b></p>
    ";

    $mail->send();

}
catch (Exception $e) {

    echo 'Mailer Error: ' . $mail->ErrorInfo;

}

/* REDIRECT */

header("Location: http://localhost/event/organiser_dashboard.php");
exit();

?>