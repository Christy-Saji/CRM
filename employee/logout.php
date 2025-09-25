<?php
session_start();
session_destroy();
header("Location: emp_login.php");
exit();
?>
