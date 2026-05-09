<?php

$conn = mysqli_connect("localhost","root","","eventdb");

$id = $_GET['id'];

$sql = "
UPDATE events
SET
superadmin_status='Rejected',
final_status='Rejected'
WHERE id='$id'
";

mysqli_query($conn,$sql);

header("Location: super_admin_dashboard.php");

?>