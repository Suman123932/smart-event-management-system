<?php
session_start();

$conn = new mysqli("localhost", "root", "", "eventdb");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_SESSION['reset_email'])) {
        die("Unauthorized access");
    }

    $email = $_SESSION['reset_email'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        die("Passwords do not match");
    }

    $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashedPassword, $email);

    if ($stmt->execute()) {
        unset($_SESSION['reset_email']);
        echo "Password updated successfully";
    } else {
        echo "Something went wrong";
    }
}
?>