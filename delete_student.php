<?php
session_start();

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'dept_admin'){
    header("Location: login.html");
    exit();
}

$conn = mysqli_connect("localhost","root","","eventdb");

if(!$conn){
    die("Connection Failed");
}

$id = intval($_GET['id']);

$query = "DELETE FROM students WHERE id='$id'";

$result = mysqli_query($conn,$query);

if($result){
    echo "
    <script>
        alert('Student Deleted Successfully');
        localStorage.setItem('activeSection','manage-students');
        window.location.href='dept_admin_dashboard.php';
    </script>
    ";
}else{
    echo "
    <script>
        alert('Failed to Delete Student');
        window.location.href='dept_admin_dashboard.php#manage-students';
    </script>
    ";
}
?>