<?php

require 'login_check.php';

include 'config_site_info.php';
include 'array_templates.php';
include 'db_connect.php';
include 'aa_functions.php';

include 'aa_header.php';

if(isset($_POST['submit']) && $_POST['submit'] == 'yes'){

	$write = $show_pass = '';

	$write .= 
'<?php
$site_url = $_SERVER[\'SERVER_NAME\'];
$page = $_SERVER[\'REQUEST_URI\'];
$site_name = \''.$site_name.'\';
$contact_name = \''.$_POST['contact_name'].'\';
$contact_email = \''.$_POST['contact_email'].'\';
$login_id = \''.$_POST['login_id'].'\';
$login_pass = \''.$_POST['login_pass'].'\';
$database = \''.$database.'\';
$default_page = \'/home.html\';
$cart_url = \'/cart.html\';
?>';

	$file = fopen('config_site_info.php', "w+");
	fwrite($file, $write);
	fclose($file);
	
	$a=0;
	$pass_length = strlen($login_pass);
	while($a < $pass_length){
		$show_pass .= '*';
		$a++;
	}
		
	echo'
	
		<div class="Title">Settings Updated</div>
		
		<div class="Label">Contact Name</div>
		<div class="LabelInsert">'.$contact_name.'</div>

		<div class="Label">Contact Email</div>
		<div class="LabelInsert">'.$contact_email.'</div>
		
		<div class="Label">Login ID</div>
		<div class="LabelInsert">'.$login_id.'</div>
		
		<div class="Label">Login Password</div>
		<div class="LabelInsert">'.$show_pass.'</div>
					
	';
}

else{
	echo'
	<form enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'" method="post">
	
		<div class="Title">Settings</div>
		
		<div class="Label">Contact Name</div>
		<div class="LabelInsert"><input type="text" name="contact_name" value="'.$contact_name.'" class="text"></div>

		<div class="Label">Contact Email</div>
		<div class="LabelInsert"><input type="text" name="contact_email" value="'.$contact_email.'" class="text"></div>
		
		<div class="Label">Login ID</div>
		<div class="LabelInsert"><input type="text" name="login_id" value="'.$login_id.'" class="text"></div>
		
		<div class="Label">Login Password</div>
		<div class="LabelInsert"><input type="password" name="login_pass" value="'.$login_pass.'" class="text"></div>
					
		<div class="Submit">
			<input type="hidden" name="submit" value="yes"/>
			<input type="submit" value="Save Changes" />
		</div>
	</form>
	';
}

include 'aa_footer.php';

?>
