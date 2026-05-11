<?php
session_start();

/* ROLE CHECK */

if (
    !isset($_SESSION['role']) ||
    $_SESSION['role'] != 'dept_admin'
) {
    header("Location: login.html");
    exit();
}

/* DATABASE CONNECTION */

$conn = mysqli_connect("localhost", "root", "", "eventdb");

if (!$conn) {
    die("Connection Failed");
}

/* SESSION DATA */

$user_id = $_SESSION['user_id'];
$name = $_SESSION['user'];
$email = $_SESSION['email'];
$department = $_SESSION['department'];

/* FETCH EVENTS CREATED BY THIS DEPT ADMIN */

$eventQuery = "

SELECT events.*,

(
SELECT COUNT(*)
FROM event_registrations
WHERE event_registrations.event_id = events.id
)

AS registered_students

FROM events

WHERE created_by='$user_id'

ORDER BY created_at DESC

";

$eventResult = mysqli_query($conn, $eventQuery);

/* COUNTS */

$pendingCountQuery = "
SELECT * FROM events
WHERE created_by='$user_id'
AND final_status='Pending'
";

$pendingCount = mysqli_num_rows(
mysqli_query($conn, $pendingCountQuery)
);

$approvedCountQuery = "
SELECT * FROM events
WHERE created_by='$user_id'
AND final_status='Approved'
";

$approvedCount = mysqli_num_rows(
mysqli_query($conn, $approvedCountQuery)
);

$rejectedCountQuery = "
SELECT * FROM events
WHERE created_by='$user_id'
AND final_status='Rejected'
";

$rejectedCount = mysqli_num_rows(
mysqli_query($conn, $rejectedCountQuery)
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Department Admin Dashboard</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
}

body{
font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;
background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);
min-height:100vh;
}

/* SIDEBAR */

.sidebar{
width:260px;
height:100vh;
background:white;
position:fixed;
left:0;
top:0;
box-shadow:2px 0 20px rgba(0,0,0,0.1);
overflow-y:auto;
}

.sidebar-header{
padding:25px;
text-align:center;
border-bottom:1px solid #eee;
}

.logo{
font-size:24px;
font-weight:bold;
color:#667eea;
margin-bottom:10px;
}

.user-info{
font-size:14px;
color:#666;
}

.nav-menu{
list-style:none;
padding:20px 0;
}

.nav-item{
padding:15px 25px;
cursor:pointer;
transition:0.3s;
border-left:4px solid transparent;
}

.nav-item:hover,
.nav-item.active{
background:linear-gradient(90deg,#667eea,#764ba2);
color:white;
border-left-color:white;
}

.nav-item i{
margin-right:12px;
}

/* MAIN */

.main-content{
margin-left:260px;
padding:25px;
}

/* SECTION */

.section{
display:none;
}

.section.active{
display:block;
}

/* CARDS */

.dashboard-grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
gap:20px;
margin-bottom:30px;
}

.card{
background:white;
padding:25px;
border-radius:20px;
box-shadow:0 10px 25px rgba(0,0,0,0.1);
}

.card-header{
display:flex;
align-items:center;
margin-bottom:20px;
}

.card-icon{
width:60px;
height:60px;
border-radius:15px;
display:flex;
align-items:center;
justify-content:center;
margin-right:15px;
font-size:22px;
color:white;
}

.pending-icon{
background:linear-gradient(45deg,#f39c12,#f1c40f);
}

.approved-icon{
background:linear-gradient(45deg,#2ecc71,#27ae60);
}

.rejected-icon{
background:linear-gradient(45deg,#e74c3c,#c0392b);
}

.card-number{
font-size:36px;
font-weight:bold;
color:#667eea;
}

/* TABLE */

.table-container{
background:white;
padding:20px;
border-radius:20px;
overflow-x:auto;
box-shadow:0 10px 25px rgba(0,0,0,0.1);
}

.events-table{
width:100%;
border-collapse:collapse;
}

.events-table th{
background:linear-gradient(45deg,#667eea,#764ba2);
color:white;
padding:15px;
text-align:left;
}

.events-table td{
padding:15px;
border-bottom:1px solid #eee;
}

/* STATUS */

.status{
padding:6px 14px;
border-radius:20px;
font-size:12px;
font-weight:bold;
}

.pending{
background:#fff3cd;
color:#856404;
}

.approved{
background:#d4edda;
color:#155724;
}

.rejected{
background:#f8d7da;
color:#721c24;
}

/* FORM */

.form-container{
background:white;
padding:30px;
border-radius:20px;
max-width:800px;
box-shadow:0 10px 25px rgba(0,0,0,0.1);
}

.form-group{
margin-bottom:20px;
}

.form-group label{
display:block;
margin-bottom:8px;
font-weight:600;
}

.form-group input,
.form-group textarea,
.form-group select{
width:100%;
padding:14px;
border:2px solid #ddd;
border-radius:12px;
font-size:15px;
}

.btn{
background:linear-gradient(45deg,#667eea,#764ba2);
color:white;
border:none;
padding:14px 20px;
border-radius:12px;
cursor:pointer;
font-weight:bold;
transition:0.3s;
}

.btn:hover{
transform:translateY(-2px);
}

/* PROFILE */

.profile-card{
background:white;
padding:30px;
border-radius:20px;
max-width:700px;
}

.profile-avatar{
width:130px;
height:130px;
border-radius:50%;
background:linear-gradient(45deg,#667eea,#764ba2);
display:flex;
align-items:center;
justify-content:center;
font-size:50px;
color:white;
margin:auto auto 20px;
}

/* MOBILE */

@media(max-width:768px){

.sidebar{
transform:translateX(-100%);
transition:0.3s;
z-index:1000;
}

.sidebar.active{
transform:translateX(0);
}

.main-content{
margin-left:0;
}

.mobile-btn{
display:block;
}

}

.mobile-btn{
display:none;
position:fixed;
top:20px;
left:20px;
z-index:1200;
background:white;
border:none;
padding:10px;
border-radius:10px;
cursor:pointer;
}

</style>

</head>

<body>

<button class="mobile-btn" onclick="toggleSidebar()">
<i class="fas fa-bars"></i>
</button>

<!-- SIDEBAR -->

<div class="sidebar" id="sidebar">

<div class="sidebar-header">

<div class="logo">
<i class="fas fa-user-cog"></i> Dept Admin
</div>

<div class="user-info">
<?php echo $name; ?>
<br>
<small><?php echo $department; ?></small>
</div>

</div>

<ul class="nav-menu">

<li class="nav-item active" onclick="showSection('dashboard',event)">
<i class="fas fa-tachometer-alt"></i> Dashboard
</li>

<li class="nav-item" onclick="showSection('create-event',event)">
<i class="fas fa-plus-circle"></i> Create Event
</li>
<!--<li class="nav-item" onclick="showSection('manage-students',event)">

<i class="fas fa-user-graduate"></i>

Manage Students

</li> -->

<li class="nav-item" onclick="showSection('my-events',event)">
<i class="fas fa-calendar-alt"></i> My Events
</li>

<li class="nav-item" onclick="showSection('analytics',event)">
<i class="fas fa-chart-line"></i> Analytics
</li>

<li class="nav-item" onclick="showSection('profile',event)">
<i class="fas fa-user-circle"></i> Profile
</li>

<li class="nav-item">
<a href="logout.php" style="text-decoration:none;color:inherit;">
<i class="fas fa-sign-out-alt"></i> Logout
</a>
</li>

</ul>

</div>

<!-- MAIN -->

<div class="main-content">

<!-- DASHBOARD -->

<div id="dashboard" class="section active">

<h1 style="color:white;margin-bottom:30px;">
Dashboard
</h1>

<div class="dashboard-grid">

<div class="card">

<div class="card-header">

<div class="card-icon pending-icon">
<i class="fas fa-clock"></i>
</div>

<div>
<h3>Pending Events</h3>
<div class="card-number">
<?php echo $pendingCount; ?>
</div>
</div>

</div>

</div>

<div class="card">

<div class="card-header">

<div class="card-icon approved-icon">
<i class="fas fa-check"></i>
</div>

<div>
<h3>Approved Events</h3>
<div class="card-number">
<?php echo $approvedCount; ?>
</div>
</div>

</div>

</div>

<div class="card">

<div class="card-header">

<div class="card-icon rejected-icon">
<i class="fas fa-times"></i>
</div>

<div>
<h3>Rejected Events</h3>
<div class="card-number">
<?php echo $rejectedCount; ?>
</div>
</div>

</div>

</div>

</div>

</div>

<!-- CREATE EVENT -->

<div id="create-event" class="section">

<h1 style="color:white;margin-bottom:30px;">
Create Event
</h1>

<div class="form-container">

<form action="create_event.php" method="POST">

<div class="form-group">
<label>Event Title</label>
<input type="text" name="title" required>
</div>

<div class="form-group">
<label>Event Date</label>
<input type="datetime-local" name="date" required>
</div>

<div class="form-group">
<label>Venue</label>
<input type="text" name="venue" required>
</div>

<div class="form-group">
<label>Category</label>

<select name="category" required>

<option value="">Select Category</option>
<option value="Technical">Technical</option>
<option value="Workshop">Workshop</option>
<option value="Seminar">Seminar</option>
<option value="Cultural">Cultural</option>

</select>

</div>

<div class="form-group">
<label>Capacity</label>
<input type="number" name="capacity" required>
</div>

<div class="form-group">
<label>Description</label>
<textarea name="description" rows="5" required></textarea>
</div>

<button type="submit" class="btn">
Create Event
</button>

</form>

</div>

</div>

<!-- MY EVENTS -->

<div id="my-events" class="section">

<h1 style="color:white;margin-bottom:30px;">
My Events
</h1>

<div class="table-container">

<table class="events-table">

<thead>

<tr>
<th>Event</th>
<th>Date</th>
<th>Venue</th>
<th>Organizer Status</th>
<th>Super Admin Status</th>
<th>Final Status</th>
<th>Registred Student</th>
</tr>

</thead>

<tbody>

<?php
while($row = mysqli_fetch_assoc($eventResult)){
?>

<tr>

<td><?php echo $row['title']; ?></td>

<td>
<?php echo date("d M Y h:i A", strtotime($row['event_date'])); ?>
</td>

<td><?php echo $row['venue']; ?></td>

<td>

<?php
if($row['organizer_status']=="Approved"){
echo "<span class='status approved'>Approved</span>";
}
elseif($row['organizer_status']=="Rejected"){
echo "<span class='status rejected'>Rejected</span>";
}
else{
echo "<span class='status pending'>Pending</span>";
}
?>

</td>

<td>

<?php
if($row['superadmin_status']=="Approved"){
echo "<span class='status approved'>Approved</span>";
}
elseif($row['superadmin_status']=="Rejected"){
echo "<span class='status rejected'>Rejected</span>";
}
else{
echo "<span class='status pending'>Pending</span>";
}
?>

</td>

<td>

<?php
if($row['final_status']=="Approved"){
echo "<span class='status approved'>Approved</span>";
}
elseif($row['final_status']=="Rejected"){
echo "<span class='status rejected'>Rejected</span>";
}
else{
echo "<span class='status pending'>Pending</span>";
}
?>

</td>
<td>

<?php echo $row['registered_students']; ?>

</td>

</tr>

<?php
}
?>

</tbody>

</table>

</div>

</div>

<!-- ANALYTICS -->

<div id="analytics" class="section">

<h1 style="color:white;margin-bottom:30px;">
Analytics
</h1>

<div class="table-container">

<canvas id="chart"></canvas>

</div>

</div>

<!-- PROFILE -->

<div id="profile" class="section">

<h1 style="color:white;margin-bottom:30px;">
My Profile
</h1>

<div class="profile-card">

<div class="profile-avatar">
<i class="fas fa-user"></i>
</div>

<div class="form-group">
<label>Name</label>
<input type="text" value="<?php echo $name; ?>" readonly>
</div>

<div class="form-group">
<label>Email</label>
<input type="text" value="<?php echo $email; ?>" readonly>
</div>

<div class="form-group">
<label>Department</label>
<input type="text" value="<?php echo $department; ?>" readonly>
</div>

<div class="form-group">
<label>Role</label>
<input type="text" value="Department Admin" readonly>
</div>

</div>

</div>

</div>
<!-- MANAGE STUDENTS 

<div id="manage-students" class="section">

<h1 style="color:white;margin-bottom:30px;">

Manage Students

</h1>

<div class="table-container">

<div style="margin-bottom:20px;">

<a href="add_student.php">

<button class="btn btn-approve">

<i class="fas fa-plus"></i>

Add Student

</button>

</a>

</div>

<table class="events-table">

<thead>

<tr>

<th>Name</th>
<th>Roll No</th>
<th>Email</th>
<th>Department</th>
<th>Action</th>


</tr>

</thead>

<tbody>

<?php

$studentQuery = "
SELECT *
FROM students
WHERE department='$department'
";

$studentResult = mysqli_query($conn,$studentQuery);

while($student=mysqli_fetch_assoc($studentResult)){

?>

<tr>

<td>
<?php echo $student['student_name']; ?>
</td>

<td>
<?php echo $student['roll_no']; ?>
</td>

<td>
<?php echo $student['email']; ?>
</td>

<td>
<?php echo $student['department']; ?>
</td>

<td>

<a href="delete_student.php?id=<?php echo $student['id']; ?>"
onclick="return confirm('Are you sure to delete this student?')">

<button
style="
background:#e74c3c;
color:white;
border:none;
padding:8px 12px;
border-radius:6px;
cursor:pointer;
">

<i class="fas fa-trash"></i> Delete

</button>

</a>

</td>

</tr>

<?php
}
?>

</tbody>

</table>

</div>

</div>
-->

<script>

function showSection(id,event=null){

document.querySelectorAll('.section').forEach(section=>{
section.classList.remove('active');
});

document.getElementById(id).classList.add('active');

document.querySelectorAll('.nav-item').forEach(item=>{
item.classList.remove('active');
});

if(event){
event.currentTarget.classList.add('active');
}

localStorage.setItem('activeSection', id);

}

window.onload = function(){

let activeSection = localStorage.getItem('activeSection');

if(activeSection){

showSection(activeSection);

let navItems = document.querySelectorAll('.nav-item');

navItems.forEach(item=>{

item.classList.remove('active');

if(
item.getAttribute('onclick') &&
item.getAttribute('onclick').includes(activeSection)
){
item.classList.add('active');
}

});

}

}
function toggleSidebar(){
document.getElementById('sidebar').classList.toggle('active');
}

/* CHART */

const ctx = document.getElementById('chart');

new Chart(ctx,{

type:'bar',

data:{
labels:[
'Pending',
'Approved',
'Rejected'
],

datasets:[{
label:'Events',
data:[
<?php echo $pendingCount; ?>,
<?php echo $approvedCount; ?>,
<?php echo $rejectedCount; ?>
]
}]
},

options:{
responsive:true
}

});

</script>

</body>

</html>