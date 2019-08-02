<?php

require 'login_check.php';

include 'config_site_info.php';
include 'array_templates.php';
include 'db_connect.php';
include 'functions.php';

include 'header.php';

if(isset($_POST['submit']) && $_POST['submit'] == 'yes'){

	if(isset($_FILES['logo']['name']) && $_FILES['logo']['name'] != ''){
		$source = $_FILES['logo']['tmp_name'];
		$basename = basename( $_FILES['logo']['name']);
		$target = $_SERVER['DOCUMENT_ROOT']."/site/".basename($_FILES['logo']['name']);
		move_uploaded_file($source, $target);

		list($width, $height) = getimagesize($target);

		$value['maxwidth'] = '600';
		$value['maxheight'] = '180';

		$resized = resizeImage($width, $height, $value);

		$imagetype = trim(substr($basename, strrpos($basename, '.')));
		$imagetype = strtolower($imagetype);

		$tn = imagecreatetruecolor($resized['modwidth'], $resized['modheight']) ;
		imagealphablending($tn, false);
		imagesavealpha($tn, true);
		if($imagetype == '.jpg' || $imagetype == '.jpeg'){
			$image = imagecreatefromjpeg($target);
			imagecopyresampled($tn, $image, 0, 0, 0, 0, $resized['modwidth'], $resized['modheight'], $width, $height);
			imagejpeg($tn, $target, 100);
		}
		elseif($imagetype == '.gif'){
			$image = imagecreatefromgif($target);
			imagecopyresampled($tn, $image, 0, 0, 0, 0, $resized['modwidth'], $resized['modheight'], $width, $height);
			imagegif($tn, $target, 100);
		}
		elseif($imagetype == '.png'){
			$image = imagecreatefrompng($target);
			imagecopyresampled($tn, $image, 0, 0, 0, 0, $resized['modwidth'], $resized['modheight'], $width, $height);
			imagepng($tn, $target, 100);
		}

		$logo = $basename;
	}
	elseif(isset($_POST['Current{}logo']) && $_POST['Current{}logo'] != ''){
		$logo = $_POST['Current{}logo'];
	}

	$write = $show_pass = '';

	$write .= 
'<?php
$site_url = $_SERVER[\'SERVER_NAME\'];
$page = $_SERVER[\'REQUEST_URI\'];
$site_name = \''.$_POST['site_name'].'\';
$logo = \''.$logo.'\';
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
		
		<div class="Label">Website Name</div>
		<div class="LabelInsert">'.$_POST['site_name'].'</div>
		
		<div class="Label">Logo</div>
		<div class="LabelInsert"><img src="/site/'.$logo.'"></div>

		<div class="Label">Contact Name</div>
		<div class="LabelInsert">'.$_POST['contact_name'].'</div>

		<div class="Label">Contact Email</div>
		<div class="LabelInsert">'.$_POST['contact_email'].'</div>
		
		<div class="Label">Login ID</div>
		<div class="LabelInsert">'.$_POST['login_id'].'</div>
		
		<div class="Label">Login Password</div>
		<div class="LabelInsert">'.$show_pass.'</div>
					
	';
}

else{
	echo'
	<form enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'" method="post">
	
		<div class="Title">Settings</div>

		<div class="Label">Website Name</div>
		<div class="LabelInsert"><input type="text" name="site_name" value="'.$site_name.'" class="text"></div>
		
		<div class="Label">Logo</div>
		<div class="LabelInsert Logo">
			';
			if(!isset($logo) || $logo == '' || !file_exists($_SERVER['DOCUMENT_ROOT'].'/site/'.$logo)){
				echo '<input type="file" name="logo" class="text">';
			}
			else{
				echo '

					<img src="/site/'.$logo.'" class="Left">

					<div class="Left">
						<h3>Change Image</h3>
						<input type="file" name="logo" class="text">
						<input type="hidden" name="Current{}logo" value="'.$logo.'" class="text">
					</div>
					<div class="Clear"></div>
					';
			}
			echo '
		</div>

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

include 'footer.php';

?>
