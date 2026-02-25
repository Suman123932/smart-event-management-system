<?php
session_start();
$conn = new mysqli("localhost", "root", "", "eventdb");

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {

    $row = $result->fetch_assoc();

    if (password_verify($password, $row['password'])) {

        $_SESSION['user'] = $row['full_name'];
        $_SESSION['role'] = $row['role'];

        if ($row['role'] == "admin") {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: student_dashboard.php");
        }
        exit();
    } else {
        echo "Wrong Password";
    }

} else {
    echo "User not found";
}
?>