<?php

session_start();

/* ROLE CHECK */

if(
!isset($_SESSION['role']) ||
$_SESSION['role'] != 'dept_admin'
){
header("Location: login.html");
exit();
}

/* DATABASE CONNECTION */

$conn = mysqli_connect("localhost","root","","eventdb");

if(!$conn){
die("Connection Failed");
}

/* FORM DATA */

$student_name = $_POST['student_name'];

$roll_no = $_POST['roll_no'];

$email = $_POST['email'];

$department = $_POST['department'];

/* CHECK DUPLICATE EMAIL */

$checkQuery = "
SELECT *
FROM students
WHERE email='$email'
";

$checkResult = mysqli_query($conn,$checkQuery);

if(mysqli_num_rows($checkResult) > 0){

echo "
<script>

alert('Student already exists');

window.location.href='add_student.php';

</script>
";

exit();

}

/* INSERT STUDENT */

$insertQuery = "
INSERT INTO students
(
student_name,
roll_no,
email,
department
)

VALUES
(
'$student_name',
'$roll_no',
'$email',
'$department'
)
";

$result = mysqli_query($conn,$insertQuery);

/* SUCCESS */

if($result){

echo "
<script>

localStorage.setItem('activeSection','manage-students');

alert('Student Added Successfully');

window.location.href='dept_admin_dashboard.php';

</script>
";

}
else{

echo "
<script>

alert('Failed To Add Student');

window.location.href='add_student.php';

</script>
";

}

?>