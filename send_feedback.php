<?php

session_start();

/* ROLE CHECK */

if(
!isset($_SESSION['role']) ||
$_SESSION['role'] != 'super_admin'
){
header("Location: login.html");
exit();
}

/* DATABASE */

$conn = mysqli_connect("localhost","root","","eventdb");

if(!$conn){
die("Connection Failed");
}

/* EVENT ID */

$event_id = $_GET['event_id'];

/* FETCH EVENT */

$eventQuery = "
SELECT *
FROM events
WHERE id='$event_id'
";

$eventResult = mysqli_query($conn,$eventQuery);

$eventData = mysqli_fetch_assoc($eventResult);

$event_title = $eventData['title'];

/* FETCH ATTENDED STUDENTS */

$attendanceQuery = "
SELECT *
FROM event_attendance
WHERE event_id='$event_id'
";

$attendanceResult = mysqli_query($conn,$attendanceQuery);

/* PHPMAILER */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

/* SEND MAILS */

while($student=mysqli_fetch_assoc($attendanceResult)){

$student_email = $student['email'];

$student_name = $student['student_name'];

$mail = new PHPMailer(true);

try{

$mail->isSMTP();

$mail->Host = 'smtp.gmail.com';

$mail->SMTPAuth = true;

$mail->Username = 'sahoosuman4500@gmail.com';

$mail->Password = 'qaruexzfblwjguwm';

$mail->SMTPSecure = 'tls';

$mail->Port = 587;

$mail->setFrom('sahoosuman4500@gmail.com','Smart Event System');

$mail->addAddress($student_email);

$mail->isHTML(true);

$mail->Subject = "Event Feedback Form";

$mail->Body = "

Hello $student_name,

Thank you for attending:

$event_title

Please provide your feedback:

http://localhost/event/feedback_form.php?event_id=$event_id

Thank You.

";

$mail->send();

}
catch(Exception $e){

}

}

/* SUCCESS */

echo "
<script>

alert('Feedback Forms Sent Successfully');

window.location.href='super_admin_dashboard.php';

</script>
";

?>