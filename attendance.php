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

$event_id = $_GET['event_id'];

/* FETCH EVENT */

$eventQuery = "
SELECT *
FROM events
WHERE id='$event_id'
";

$eventResult = mysqli_query($conn,$eventQuery);

$eventData = mysqli_fetch_assoc($eventResult);

/* FETCH REGISTERED STUDENTS */

$studentQuery = "
SELECT *
FROM event_registrations
WHERE event_id='$event_id'
";

$studentResult = mysqli_query($conn,$studentQuery);

?>

<!DOCTYPE html>
<html>

<head>

<title>Attendance Management</title>

<style>

body{
font-family:Arial;
background:#f4f4f4;
padding:40px;
}

.container{
background:white;
padding:30px;
border-radius:10px;
max-width:1000px;
margin:auto;
box-shadow:0 0 10px rgba(0,0,0,0.1);
}

h2{
margin-bottom:20px;
color:#333;
}

table{
width:100%;
border-collapse:collapse;
margin-top:20px;
}

table th{
background:#667eea;
color:white;
padding:12px;
}

table td{
padding:12px;
border-bottom:1px solid #ddd;
}

button{
background:#2ecc71;
color:white;
padding:12px 20px;
border:none;
border-radius:6px;
cursor:pointer;
margin-top:20px;
}

</style>

</head>

<body>

<div class="container">

<h2>

Attendance Management

</h2>

<h3>

Event:
<?php echo $eventData['title']; ?>

</h3>

<form action="save_attendance.php" method="POST">
<input
type="hidden"
name="event_id"
value="<?php echo $event_id; ?>"
>

<table>

<thead>

<tr>

<th>Present</th>
<th>Name</th>
<th>Roll No</th>
<th>Email</th>
<th>Department</th>

</tr>

</thead>

<tbody>

<?php
while($row=mysqli_fetch_assoc($studentResult)){
?>

<tr>

<td>

<input
type="checkbox"
name="attendance[]"
value="<?php echo $row['id']; ?>"
>

</td>

<td>
<?php echo $row['student_name']; ?>
</td>

<td>
<?php echo $row['roll_no']; ?>
</td>

<td>
<?php echo $row['email']; ?>
</td>

<td>
<?php echo $row['department']; ?>
</td>

</tr>

<?php
}
?>

</tbody>

</table>

<button type="submit">

Save Attendance

</button>

</form>

</div>

</body>

</html>