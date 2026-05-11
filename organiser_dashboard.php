<?php
session_start();

/* ROLE BASED ACCESS CONTROL */

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
    die("Database connection failed");
}

/* SESSION DATA */

$organizerName = $_SESSION['user'];
$organizerEmail = $_SESSION['email'];

/* FETCH PENDING EVENTS */

$pendingQuery = "

SELECT events.*,

(
SELECT COUNT(*)
FROM event_registrations
WHERE event_registrations.event_id = events.id
)

AS registered_students

FROM events

WHERE organizer_status='Pending'

";

$pendingResult = mysqli_query($conn, $pendingQuery);

$pendingCount = mysqli_num_rows($pendingResult);

/* FETCH APPROVED EVENTS */

$approvedQuery = "
SELECT * FROM events
WHERE organizer_status='Approved'
";

$approvedResult = mysqli_query($conn, $approvedQuery);

$approvedCount = mysqli_num_rows($approvedResult);

/* FETCH REJECTED EVENTS */

$rejectedQuery = "
SELECT * FROM events
WHERE organizer_status='Rejected'
";

$rejectedResult = mysqli_query($conn, $rejectedQuery);

$rejectedCount = mysqli_num_rows($rejectedResult);

/* FETCH APPROVED EVENTS TABLE */

$approvedEventsQuery = "

SELECT events.*,

(
SELECT COUNT(*)
FROM event_registrations
WHERE event_registrations.event_id = events.id
)

AS registered_students

FROM events

WHERE organizer_status='Approved'

ORDER BY created_at DESC

";

$approvedEventsResult = mysqli_query($conn, $approvedEventsQuery);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Organizer Dashboard</title>

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
z-index:1000;
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

/* BUTTONS */

.btn{
padding:10px 18px;
border:none;
border-radius:10px;
cursor:pointer;
font-weight:bold;
color:white;
margin-right:5px;
transition:0.3s;
}

.btn:hover{
transform:translateY(-2px);
}

.btn-approve{
background:#2ecc71;
}

.btn-reject{
background:#e74c3c;
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

.form-group{
margin-bottom:20px;
}

.form-group label{
display:block;
margin-bottom:8px;
font-weight:600;
}

.form-group input{
width:100%;
padding:14px;
border:2px solid #ddd;
border-radius:12px;
}

/* MOBILE */

@media(max-width:768px){

.sidebar{
transform:translateX(-100%);
transition:0.3s;
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
<i class="fas fa-user-tie"></i> Organizer
</div>

<div class="user-info">
<?php echo $organizerName; ?>
<br>
<small>Event Organizer</small>
</div>

</div>

<ul class="nav-menu">

<li class="nav-item active" onclick="showSection('dashboard',event)">
<i class="fas fa-tachometer-alt"></i> Dashboard
</li>

<li class="nav-item" onclick="showSection('manage-events',event)">
<i class="fas fa-calendar-check"></i> Manage Events
</li>

<li class="nav-item" onclick="showSection('approved-events',event)">
<i class="fas fa-calendar-alt"></i> Approved Events
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
Organizer Dashboard
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

<!-- MANAGE EVENTS -->

<div id="manage-events" class="section">

<h1 style="color:white;margin-bottom:30px;">
Manage Events
</h1>

<div class="table-container">

<table class="events-table">

<thead>

<tr>
<th>Event</th>
<th>Department</th>
<th>Venue</th>
<th>Date</th>
<th>Status</th>
<th>Registered Students</th>
<th>Actions</th>
</tr>

</thead>

<tbody>

<?php
while($row = mysqli_fetch_assoc($pendingResult)){
?>

<tr>

<td><?php echo $row['title']; ?></td>

<td><?php echo $row['department']; ?></td>

<td><?php echo $row['venue']; ?></td>

<td>
<?php echo date("d M Y h:i A", strtotime($row['event_date'])); ?>
</td>

<td>
<span class="status pending">
Pending
</span>
</td>
<td>

<?php echo $row['registered_students']; ?>

</td>

<td>

<a href="approve_organizer.php?id=<?php echo $row['id']; ?>">

<button class="btn btn-approve">
<i class="fas fa-check"></i> Approve
</button>

</a>

<a href="reject_organizer.php?id=<?php echo $row['id']; ?>">

<button class="btn btn-reject">
<i class="fas fa-times"></i> Reject
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

<!-- APPROVED EVENTS -->

<div id="approved-events" class="section">

<h1 style="color:white;margin-bottom:30px;">
Approved Events
</h1>

<div class="table-container">

<table class="events-table">

<thead>

<tr>
<th>Event</th>
<th>Department</th>
<th>Venue</th>
<th>Date</th>
<th>Status</th>
<th>Registered Students</th>
<th>Attendance</th>
</tr>

</thead>

<tbody>

<?php
while($approvedRow = mysqli_fetch_assoc($approvedEventsResult)){
?>

<tr>

<td><?php echo $approvedRow['title']; ?></td>

<td><?php echo $approvedRow['department']; ?></td>

<td><?php echo $approvedRow['venue']; ?></td>

<td>
<?php echo date("d M Y", strtotime($approvedRow['event_date'])); ?>
</td>

<td>
<span class="status approved">
Approved
</span>
</td>
<td>

<?php echo $approvedRow['registered_students']; ?>

</td>

<td>

<a href="attendance.php?event_id=<?php echo $approvedRow['id']; ?>">

<button class="btn btn-approve">

<i class="fas fa-user-check"></i>

Attendance

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
<input type="text" value="<?php echo $organizerName; ?>" readonly>
</div>

<div class="form-group">
<label>Email</label>
<input type="text" value="<?php echo $organizerEmail; ?>" readonly>
</div>

<div class="form-group">
<label>Role</label>
<input type="text" value="Event Organizer" readonly>
</div>

</div>

</div>

</div>

<script>

function showSection(id,event){

document.querySelectorAll('.section').forEach(section=>{
section.classList.remove('active');
});

document.getElementById(id).classList.add('active');

document.querySelectorAll('.nav-item').forEach(item=>{
item.classList.remove('active');
});

event.currentTarget.classList.add('active');

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