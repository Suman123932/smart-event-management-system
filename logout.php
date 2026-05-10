<?php

session_start();

/* DESTROY SESSION */

session_unset();

session_destroy();

/* REDIRECT TO LOGIN PAGE */

header("Location: http://localhost/event/login.html");

exit();

?>