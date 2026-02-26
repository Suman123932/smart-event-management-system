<?php
session_start();
$conn = new mysqli("localhost", "root", "", "eventdb");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $role = strtolower($_POST['role']);
    $password = $_POST['password'];

    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>
                alert('Email already registered!');
                window.location='registration.html';
              </script>";
        exit();
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (full_name,email,role,password) VALUES (?,?,?,?)");
    $stmt->bind_param("ssss", $full_name, $email, $role, $hashedPassword);
    if ($stmt->execute()) {
        echo "<script>
                alert('Registration successful! Please login.');
                window.location='login.html';
              </script>";
    } else {
        echo "<script>
                alert('Registration failed!');
                window.location='registration.html';
              </script>";
    }
}
?>