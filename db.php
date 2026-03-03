<?php
$conn = new mysqli("localhost","root","","eventdb");

if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}
?>