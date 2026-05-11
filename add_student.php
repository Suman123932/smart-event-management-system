<?php

session_start();

/* ROLE CHECK */

if(
!isset($_SESSION['role']) ||
$_SESSION['role'] != 'dept_admin'
){
header("Location: login.html");
exit();
}

/* DATABASE */

$conn = mysqli_connect("localhost","root","","eventdb");

if(!$conn){
die("Connection Failed");
}

/* DEPARTMENT */

$department = $_SESSION['department'];

?>

<!DOCTYPE html>
<html>

<head>

<title>Add Student</title>

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

input{
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

<h2>Add Student</h2>

<form action="save_student.php" method="POST">

<label>Student Name</label>
<input type="text" name="student_name" required>

<label>Roll Number</label>
<input type="text" name="roll_no" required>

<label>Email</label>
<input type="email" name="email" required>

<label>Department</label>

<input
type="text"
name="department"
value="<?php echo $department; ?>"
readonly
>

<button type="submit">

Add Student

</button>

</form>

</div>

</body>

</html>