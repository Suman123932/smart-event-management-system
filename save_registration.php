<?php

/* DATABASE CONNECTION */

$conn = mysqli_connect("localhost", "root", "", "eventdb");

if (!$conn) {
    die("Connection Failed");
}

/* FORM DATA */

$event_id = $_POST['event_id'];

$student_name = $_POST['student_name'];

$roll_no = $_POST['roll_no'];

$email = $_POST['email'];

$department = $_POST['department'];

/* CHECK DUPLICATE REGISTRATION */

$checkQuery = "
SELECT *
FROM event_registrations
WHERE
event_id='$event_id'
AND email='$email'
";

$checkResult = mysqli_query($conn, $checkQuery);

if (mysqli_num_rows($checkResult) > 0) {

    echo "
    <script>

    alert('You have already registered for this event');

    window.location.href='student_register.php';

    </script>
    ";

    exit();
}

/* INSERT REGISTRATION */

$insertQuery = "
INSERT INTO event_registrations
(
event_id,
student_name,
roll_no,
email,
department
)

VALUES
(
'$event_id',
'$student_name',
'$roll_no',
'$email',
'$department'
)
";

$result = mysqli_query($conn, $insertQuery);

/* SUCCESS MESSAGE */

if ($result) {

    echo "
    <script>

    alert('Registration Successful');

    window.location.href='student_register.php';

    </script>
    ";

} else {

    echo "
    <script>

    alert('Registration Failed');

    window.location.href='student_register.php';

    </script>
    ";
}

?>