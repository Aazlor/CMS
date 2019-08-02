<?php

include 'config_site_info.php';

session_start();

if(!isset($_SESSION['login_name']) || $_SESSION['login_name'] != $login_id || !isset($_SESSION['login_password']) || $_SESSION['login_password'] != $login_pass){
	include 'login.php';
	exit;
}
?>
