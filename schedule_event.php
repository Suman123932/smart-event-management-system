<?php

// DATABASE CONNECTION
$conn = new mysqli("localhost","root","","hall_available");

if($conn->connect_error){
die("Connection Failed: ".$conn->connect_error);
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Smart Hall Scheduling</title>

<style>

body{
font-family: Arial;
background:#f2f2f2;
text-align:center;
}

form{
background:white;
padding:20px;
width:300px;
margin:auto;
box-shadow:0px 0px 10px gray;
}

input,select{
width:90%;
padding:8px;
margin:5px;
}

button{
padding:10px;
background:#007bff;
color:white;
border:none;
cursor:pointer;
}

</style>

</head>

<body>

<h2>Event Scheduling System</h2>

<form method="POST">

Event Name<br>
<input type="text" name="event_name" required><br>

Select Hall<br>

<select name="hall_id">

<?php

$result=$conn->query("SELECT * FROM halls");

while($row=$result->fetch_assoc()){
echo "<option value='".$row['id']."'>".$row['hall_name']."</option>";
}

?>

</select><br>

Event Date<br>
<input type="date" name="event_date" required><br>

Start Time<br>
<input type="time" name="start_time" required><br>

End Time<br>
<input type="time" name="end_time" required><br>

<button type="submit" name="submit">Schedule Event</button>

</form>

</body>
</html>

<?php

if(isset($_POST['submit'])){

$name=$_POST['event_name'];
$hall=$_POST['hall_id'];
$date=$_POST['event_date'];
$start=$_POST['start_time'];
$end=$_POST['end_time'];

$check="SELECT * FROM events
WHERE hall_id='$hall'
AND event_date='$date'
AND (start_time < '$end' AND end_time > '$start')";

$result=$conn->query($check);

if($result->num_rows>0){

echo "<h3 style='color:red;text-align:center;'>Hall Already Booked</h3>";

}
else{

$insert="INSERT INTO events(event_name,hall_id,event_date,start_time,end_time)
VALUES('$name','$hall','$date','$start','$end')";

$conn->query($insert);

echo "<h3 style='color:green;text-align:center;'>Event Scheduled Successfully</h3>";

}

}

?>