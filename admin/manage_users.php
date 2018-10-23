<?php

include('header.php');

$tabs = array(
	0 => "add",
	1 => "edit",
	2 => "remove",
);

$tab_title = 'Manage Users';
foreach($tabs as $value){
	unset($active);
	if($_GET['x'] == $value){
		$active = ' class="active"';
		$tab_title .= ' - '.ucwords($value);
	}
	$get_tabs .= '<li'.$active.'><a href="/admin/manage_users.php?x='.$value.'">'.ucwords($value).' User</a></li>';
}
echo'


<div class="Title">'.$tab_title.'</div>

<div id="Tabs">
	'.$get_tabs.'
	<div class="Clear"></div>
</div>
';

if($_POST["submit_add"] == "yes") {
	
	foreach($_POST as $key => $value){
		if($key != 'submit_add'){
			$info .= '{{}}'.$key.'(())'.$value;
		}
	}
	
	if($mysqli->query("INSERT INTO $database (type, name, relation, info) VALUES ('user', '$_POST[User]', '$_POST[Email]', '$info')")){
		echo '<div class="Title">User Added - '.$_POST[User].'</div>';
	}
	else{
		echo 'ERROR';
	}
}

if($_POST["submit_edit"] == "yes"){
	
	foreach($_POST as $key => $value){
		if($key != 'submit_add'){
			$info .= '{{}}'.$key.'(())'.$value;
		}
	}
	
	if($mysqli->query("UPDATE $database SET name='$_POST[User]', relation='$_POST[Email]', info='$info' WHERE id='$_POST[id]'")){
		echo '<div class="Title">User Edited - '.$_POST[User].'</div>';
	}
	else{
		echo 'ERROR';
	}
}

if($_POST["Remove"] == "Delete"){
	if($mysqli->query("DELETE FROM $database WHERE id='$_POST[id]'")){
		echo '<div class="Title">User Removed - '.$_POST[User].'</div>';
	}
	else{
		echo 'ERROR';
	}
}


if($_GET['x'] == 'add'){
	echo '
	<form method="POST" enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'">
		<div class="Label">User Name</div>
		<div class="LabelInsert">
			<input type="text" name="User" value="" class="text">
		</div>
		
		<div class="Label">Email Address</div>
		<div class="LabelInsert">
			<input type="text" name="Email" value="" class="text">
		</div>
		
		<div class="Label">Password</div>
		<div class="LabelInsert">
			<input type="text" name="Password" value="" class="text">
		</div>

		<div class="Label">Projects User Has Access To</div>
		<div class="LabelInsert">
		';
		
		$dir = "../projects/";
		
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					if($file != '.' && $file != '..'){
						echo '<input type="checkbox" name="Project_'.$file.'" value="'.$file.'"> '.$file.'<br>';
					}
				}
				closedir($dh);
			}
		}
		
		echo'
		</div>
		<div class="Submit">
			<input type="hidden" name="submit_add" value="yes"/>
			<input type="submit" value="Add User" />
		</div>
	</form>

	';	
}

if($_GET['x'] == 'edit'){
	if($_GET['y'] == ''){
		$get_users = $mysqli->query("SELECT * FROM $database WHERE type='user' ORDER BY name ASC");
		
		while($user = mysql_fetch_row($get_users)){
			echo'
				<div>
					<div style="float: left;">'.$user[2].'</div>
					<div style="float: right;">
						<a href="?x=edit&y='.$user[0].'">Edit</a>
					</div>
					<div style="clear:both;"></div>
					<hr>
				</div>
			';
		}
	}
	elseif($_GET['y'] != ''){
		$get_user = $mysqli->query("SELECT * FROM $database WHERE id='$_GET[y]'");
		$user = mysql_fetch_row($get_user);
		
		$split = preg_split('/{{}}/', $user[4]);
		foreach($split as $value){
			$split2 = preg_split('/\(\(\)\)/', $value);
			$user_info[$split2[0]] = $split2[1];
		}
		
		echo '
		<form method="POST" enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'?x=edit">
			<div class="Label">User Name</div>
			<div class="LabelInsert">
				<input type="text" name="User" value="'.$user_info[User].'" class="text">
			</div>
			
			<div class="Label">Email Address</div>
			<div class="LabelInsert">
				<input type="text" name="Email" value="'.$user_info[Email].'" class="text">
			</div>
			
			<div class="Label">Password</div>
			<div class="LabelInsert">
				<input type="text" name="Password" value="'.$user_info[Password].'" class="text">
			</div>
	
			<div class="Label">Projects User Has Access To</div>
			<div class="LabelInsert">
			';
			
			$dir = "../projects/";
			
			if (is_dir($dir)) {
				if ($dh = opendir($dir)) {
					while (($file = readdir($dh)) !== false) {
						if($file != '.' && $file != '..'){
							$check = 'Project_'.$file;
							if($user_info[$check] != ''){
								echo '<input type="checkbox" name="Project_'.$file.'" value="'.$file.'" checked="checked"> '.$file.'<br>';
							}
							else{
								echo '<input type="checkbox" name="Project_'.$file.'" value="'.$file.'"> '.$file.'<br>';
							}
						}
					}
					closedir($dh);
				}
			}
			
			echo'
			</div>
			<div class="Submit">
				<input type="hidden" name="submit_edit" value="yes"/>
				<input type="hidden" name="id" value="'.$_GET[y].'"/>
				<input type="submit" value="Edit User" />
			</div>
		</form>
		';	
	}
}

if($_GET['x'] == 'remove'){
	$get_users = $mysqli->query("SELECT * FROM $database WHERE type='user' ORDER BY name ASC");
	
	while($user = mysql_fetch_row($get_users)){
		echo'
			<div>
				<form method="POST" enctype="multipart/form-data" name="image_upload_form" action="'.$_SERVER["PHP_SELF"].'?x=remove">
					<div style="float: left;">'.$user[2].'</div>
					<div style="float: right;">
						<form method="POST" enctype="multipart/form-data" name="image_upload_form" action="'.$_SERVER["PHP_SELF"].'">
							<input type="submit" name="Remove" value="Delete" onClick="return confirm(\'Are you sure you want to delete '.$user[2].'?\')">
							<input type="hidden" name="User" value="'.$user[2].'">
							<input type="hidden" name="id" value="'.$user[0].'">
						</form>
					</div>
					<div style="clear:both;"></div>
					<hr>
				</form>
			</div>
		';
	}
}


include('footer.php');

?>