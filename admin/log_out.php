<?php
foreach($_POST as $key => $value)	{$$key = $value;}

session_start();

session_destroy();

header("Location: login.php");
?>