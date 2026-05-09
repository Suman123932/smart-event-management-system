<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "eventdb");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/* FORM SUBMIT */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = trim($_POST['full_name']);

    $email = trim($_POST['email']);

    $role = trim($_POST['role']);

    $departmentGroup =
    $_POST['departmentGroup'] ?? '';

    $password = $_POST['password'];

    $confirmPassword =
    $_POST['confirmPassword'];

    /* PASSWORD CHECK */

    if ($password !== $confirmPassword) {

        die("Passwords do not match");

    }

    /* EMAIL CHECK */

    $check =
    $conn->prepare(
    "SELECT user_id
     FROM users
     WHERE email = ?");

    $check->bind_param("s", $email);

    $check->execute();

    $check->store_result();

    if ($check->num_rows > 0) {

        die("Email already exists");

    }

    /* HASH PASSWORD */

    $hashedPassword =
    password_hash($password,
    PASSWORD_DEFAULT);

    /* INSERT */

    $stmt =
    $conn->prepare(
    "INSERT INTO users
    (full_name, email, role, departmentGroup, password)

    VALUES (?, ?, ?, ?, ?)");

    $stmt->bind_param(
    "sssss",
    $full_name,
    $email,
    $role,
    $departmentGroup,
    $hashedPassword
    );

    if ($stmt->execute()) {

        echo "
        <script>

        alert('Registration Successful');

        window.location.href='login.html';

        </script>
        ";

    }

    else {

        echo "Error: " . $stmt->error;

    }

}

?>