<?php
$conn = new mysqli("localhost", "root", "", "eventdb");

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

$userId = $_POST['userId'];
$fullName = $_POST['fullName'];
$email = $_POST['email'];
$role = $_POST['role'];
$password = $_POST['password'];

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (user_id, full_name, email, role, password)
        VALUES ('$userId', '$fullName', '$email', '$role', '$hashedPassword')";

if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Registration Successful!'); window.location='registration.html';</script>";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>