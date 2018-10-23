<?php
require('config_site_info.php');

if(isset($logged) && $logged == "X")	{$error .= '<tr><td class="error" colspan=3 align="center">You must log in to do that!</td></tr>';}

if(isset($_POST['submit_value']) && $_POST['submit_value'] == "login")
{	
	if($_POST['login_name'] != $login_id || $_POST['login_password'] != $login_pass){
		if(!isset($error))
			$error = '';
		$error .= '<tr><td class="error" colspan=3 align="center">The username or password you provided is incorrect.</td></tr>';
	}
			
	if(!isset($error))
	{
		session_start();
		$_SESSION['login_name'] = $login_id;
		$_SESSION['login_password'] = $login_pass;

		$login = 1;
		header("Location: /admin/");
	}
}

$form = '<form enctype="multipart/form-data" action="/admin/login.php" method="post">';

if(!isset($login) || $login != 1)
{

	$error = (isset($error)) ? $error : '';
	$login_username = (isset($login_username)) ? $login_username : '';
	$login_password = (isset($login_password)) ? $login_password : '';


echo	$form.'
		<table class="manage" width=330 align="center">
			'.$error.'
			<tr>
				<td align=right class="login" class="manage">Login:</td><td align=left><input type="text" name="login_name" value="'.$login_username.'"></td>
			</tr><tr>
				<td align=right class="login" class="manage">Password:</td><td align=left><input type="password" name="login_password" value="'.$login_password.'"></td>
			</tr>
			<tr>
				<td></td>
				<td align=left>
				<input type="hidden" name="submit_value" value="login">
				<input type="submit" name="submit" value="Enter">
				</td>
			</tr>
		</table>
		</form>';
}
?>