<?php

/* DATABASE CONNECTION */

$conn = mysqli_connect("localhost","root","","eventdb");

if(!$conn){
die("Connection Failed");
}

/* FORM DATA */

$event_id = $_POST['event_id'];

$student_name = $_POST['student_name'];

$email = $_POST['email'];

$rating = $_POST['rating'];

$comments = $_POST['comments'];

/* CHECK DUPLICATE FEEDBACK */

$checkQuery = "
SELECT *
FROM feedback
WHERE
event_id='$event_id'
AND email='$email'
";

$checkResult = mysqli_query($conn,$checkQuery);

if(mysqli_num_rows($checkResult) > 0){

echo "
<script>

alert('Feedback already submitted');

window.history.back();

</script>
";

exit();

}

/* INSERT FEEDBACK */

$insertQuery = "
INSERT INTO feedback
(
event_id,
student_name,
email,
rating,
comments
)

VALUES
(
'$event_id',
'$student_name',
'$email',
'$rating',
'$comments'
)
";

$result = mysqli_query($conn,$insertQuery);

/* SUCCESS */

if($result){

echo "
<script>

alert('Feedback Submitted Successfully');

window.location.href='feedback_form.php?event_id=$event_id';

</script>
";

}
else{

echo "
<script>

alert('Failed To Submit Feedback');

window.history.back();

</script>
";

}

?>