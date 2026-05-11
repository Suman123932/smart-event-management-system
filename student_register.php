<?php

$conn = mysqli_connect("localhost","root","","eventdb");

if(!$conn){
die("Connection Failed");
}

/* FETCH EVENTS */

$eventQuery = "
SELECT *
FROM events
WHERE final_status='Approved'
";

$eventResult = mysqli_query($conn,$eventQuery);

?>

<!DOCTYPE html>
<html>

<head>

<title>Student Event Registration</title>

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
max-width:600px;
margin:auto;
box-shadow:0 0 10px rgba(0,0,0,0.1);
}

input,
select{
width:100%;
padding:12px;
margin-top:10px;
margin-bottom:20px;
border:1px solid #ccc;
border-radius:6px;
}

button{
background:#667eea;
color:white;
padding:12px 20px;
border:none;
border-radius:6px;
cursor:pointer;
}

</style>

</head>

<body>

<div class="container">

<h2>Event Registration Form</h2>

<form action="save_registration.php" method="POST">

<label>Select Event</label>

<select name="event_id" required>

<option value="">Choose Event</option>

<?php
while($row=mysqli_fetch_assoc($eventResult)){
?>

<option value="<?php echo $row['id']; ?>">

<?php echo $row['title']; ?>

</option>

<?php
}
?>

</select>

<label>Student Name</label>
<input type="text" name="student_name" required>

<label>Roll Number</label>
<input type="text" name="roll_no" required>

<label>Email</label>
<input type="email" name="email" required>

<label>Department</label>
<input type="text" name="department" required>

<button type="submit">

Register

</button>

</form>

</div>

</body>

</html>