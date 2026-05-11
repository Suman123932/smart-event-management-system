<?php

$conn = mysqli_connect("localhost","root","","eventdb");

if(!$conn){
die("Connection Failed");
}

/* EVENT ID */

$event_id = $_GET['event_id'];

?>

<!DOCTYPE html>
<html>

<head>

<title>Event Feedback Form</title>

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
textarea,
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

<h2>Event Feedback Form</h2>

<form action="save_feedback.php" method="POST">

<input
type="hidden"
name="event_id"
value="<?php echo $event_id; ?>"
>

<label>Student Name</label>

<input
type="text"
name="student_name"
required
>

<label>Email</label>

<input
type="email"
name="email"
required
>

<label>Rating</label>

<select name="rating" required>

<option value="">Select Rating</option>

<option value="5">Excellent</option>
<option value="4">Very Good</option>
<option value="3">Good</option>
<option value="2">Average</option>
<option value="1">Poor</option>

</select>

<label>Comments</label>

<textarea
name="comments"
rows="5"
required
></textarea>

<button type="submit">

Submit Feedback

</button>

</form>

</div>

</body>

</html>