<?php

session_start();

/* ROLE CHECK */

if(
!isset($_SESSION['role']) ||
$_SESSION['role'] != 'event_organizer'
){
header("Location: login.html");
exit();
}

/* DATABASE CONNECTION */

$conn = mysqli_connect("localhost","root","","eventdb");

if(!$conn){
die("Connection Failed");
}

/* EVENT ID */

$event_id = $_POST['event_id'];

/* CHECK IF ATTENDANCE EXISTS */

$checkAttendance = "
SELECT *
FROM event_attendance
WHERE event_id='$event_id'
";

$checkResult = mysqli_query($conn,$checkAttendance);

if(mysqli_num_rows($checkResult) > 0){

echo "
<script>

alert('Attendance already marked for this event');

window.location.href='organiser_dashboard.php';

</script>
";

exit();

}

/* CHECKED STUDENTS */

if(isset($_POST['attendance'])){

$attendance = $_POST['attendance'];

foreach($attendance as $registration_id){

/* FETCH STUDENT DETAILS */

$studentQuery = "
SELECT *
FROM event_registrations
WHERE id='$registration_id'
";

$studentResult = mysqli_query($conn,$studentQuery);

$studentData = mysqli_fetch_assoc($studentResult);

/* STUDENT DATA */

$student_name = $studentData['student_name'];

$roll_no = $studentData['roll_no'];

$email = $studentData['email'];

$department = $studentData['department'];

/* INSERT ATTENDANCE */

$insertQuery = "
INSERT INTO event_attendance
(
event_id,
student_name,
roll_no,
email,
department,
attendance_status
)

VALUES
(
'$event_id',
'$student_name',
'$roll_no',
'$email',
'$department',
'Present'
)
";

mysqli_query($conn,$insertQuery);

}

}

/* SUCCESS */

echo "
<script>

alert('Attendance Saved Successfully');

window.location.href='organiser_dashboard.php';

</script>
";

?>