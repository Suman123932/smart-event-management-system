<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
$conn = new mysqli("localhost", "root", "", "eventdb");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_POST['userId'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($password !== $confirmPassword) {
        die("Passwords do not match");
    }

    // 🔍 CHECK IF EMAIL EXISTS
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        die("Your email already exists");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (user_id, full_name, email, role, password)
                            VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $user_id, $full_name, $email, $role, $hashedPassword);

    if ($stmt->execute()) {
        echo "Registration successful";
    } else {
        echo "Something went wrong";
    }
}
?>