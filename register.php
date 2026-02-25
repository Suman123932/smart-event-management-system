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


$stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $full_name, $email, $hashedPassword, $role);

if ($stmt->execute()) {
    echo "<script>
            alert('Registration Successful!');
            window.location='login.html';
          </script>";
} else {
    echo "<script>
            alert('Email already registered!');
            window.location='registration.html';
          </script>";
}

$conn->close();
?>