<?php

$conn = new mysqli("localhost","root","","hall_available");

if($conn->connect_error){
die("Connection Failed: ".$conn->connect_error);
}

?>

<!DOCTYPE html>
<html>
<head>
<title>View Halls</title>

<style>

body{
font-family: Arial;
background:#f2f2f2;
text-align:center;
}

table{
margin:auto;
border-collapse:collapse;
width:60%;
background:white;
}

th,td{
padding:10px;
border:1px solid gray;
}

th{
background:#28a745;
color:white;
}

</style>

</head>

<body>

<h2>Available Halls</h2>

<table>

<tr>
<th>ID</th>
<th>Hall Name</th>
<th>Capacity</th>
</tr>

<?php

$sql="SELECT * FROM halls";

$result=$conn->query($sql);

if($result->num_rows>0){

while($row=$result->fetch_assoc()){

echo "<tr>";
echo "<td>".$row['id']."</td>";
echo "<td>".$row['hall_name']."</td>";
echo "<td>".$row['capacity']."</td>";
echo "</tr>";

}

}
else{

echo "<tr><td colspan='3'>No Halls Found</td></tr>";

}

?>

</table>

</body>
</html>