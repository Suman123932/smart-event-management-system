<?php
// Prevent direct access if not POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: login.html");
    exit();
}

session_start();
$conn = new mysqli("localhost", "root", "", "eventdb");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

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
        $_SESSION['role'] = strtolower($row['role']);

        // Decide dashboard
        $redirect = "";
        switch ($_SESSION['role']) {
            case "super_admin": $redirect = "super_admin_dashboard.php"; break;
            case "dept_admin": $redirect = "dept_admin_dashboard.php"; break;
            case "event_organizer": $redirect = "organizer_dashboard.php"; break;
            case "student": $redirect = "student_dashboard.php"; break;
            default: $redirect = "login.html"; break;
        }

        // Popup + redirect
        echo "<script>
                alert('Welcome {$_SESSION['user']}! You are logged in as {$_SESSION['role']}');
                window.location='$redirect';
              </script>";
        exit();

    } else {
        echo "<script>
                alert('Wrong email or password!');
                window.location='login.html';
              </script>";
    }
} else {
    echo "<script>
            alert('Wrong email or password!');
            window.location='login.html';
          </script>";
}
?>