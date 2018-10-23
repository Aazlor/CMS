<?

require 'login_check.php';

include 'config_site_info.php';
include 'array_templates.php';
include 'db_connect.php';
include 'aa_functions.php';

if(isset($_POST['submit_add_gallery']) && $_POST['submit_add_gallery'] == 'yes'){

	$gallery_vars = $Gallery[$_POST['field_name']];

	switch ($_POST['type']) {
		case 'Product':
			$file_path = $_SERVER['DOCUMENT_ROOT'].'/products/'.$_POST['id'].'/'.$_POST['field_name'].'.php';
			$target = $_SERVER['DOCUMENT_ROOT'].'/products/'.$_POST['id'];
			break;
		
		default:	#pages
			$file_path = $_SERVER['DOCUMENT_ROOT'].'/gallery/'.$_POST['field_name'].'/images.php';
			$target = $_SERVER['DOCUMENT_ROOT'].'/gallery/'.$_POST['field_name'];
			break;
	}
	require $file_path;

	$files_parsed = handleFiles($_FILES, $gallery_vars, $target);

	$i=0;
	foreach($gallery_vars as $key => $value){
		if(!preg_match('/{.*}/', $key)){
			$get_keys[$i] = $key;
			$i++;
		}
	}

	$i=0;
	if(!empty($gallery_array)){
		foreach($gallery_array as $value){
			$keys[$i] = $value;
			$i++;
		}
	}
	
	foreach($gallery_vars as $key => $value){
		if(!isset($keys[$i]))
			$keys[$i] = '';

		if(stristr($key, '{image}')){
			$image_key_name = str_ireplace('{image}', '', $key);
			$keys[$i] .= '||'.$files_parsed[$image_key_name];
		}
		elseif($value == '{file}'){
			$keys[$i] .= '||'.$files_parsed[$key];
		}
		elseif(preg_match('/{select}/i', $key)){
			$key = str_ireplace('{select}', '', $key);
			$content = htmlspecialchars($_POST[$key]);
			// $content = preg_replace("/'/", '&#39;', $_POST[$key]);
			// $content = preg_replace("/\"/", '&#34;', $content);
			$keys[$i] .= '||'.$content;
		}
		else{
			if(isset($_POST[$key])){
				// $content = preg_replace('/\'/', '&#39;', $_POST[$key]);
				$content = htmlspecialchars($_POST[$key]);
				$content = stripslashes($content);
				$keys[$i] .= '||'.$content;
			}
			elseif(isset($_FILES[$key])){
				// $content = preg_replace('/\'/', '&#39;', $_FILES[$key]['name']);
				$content = htmlspecialchars($_POST[$key]);
				$content = stripslashes($content);
				$keys[$i] .= '||'.$content;
			}
		}
	}
	$keys[$i] = substr($keys[$i], 2);

	$writearray = '<?php  $gallery_array = array(';
	foreach($keys as $key => $value){
		$value = str_replace("'", "\'", $value);
		$writearray .= $key.' => \''.$value.'\',';
	}	
	$writearray .= ');  ?>';

	$file = fopen($file_path, "w+");
	fwrite($file, $writearray);
	fclose($file);	

	$show_field = str_ireplace('_', ' ', $_POST['field_name']);

	if(!isset($error)){
		$_SESSION['post_response'] = '<div class="Message"><img src="images/tick.gif"> Your '.$show_field.' upload was successful.</div>';
	}
	else{
		foreach($error_code as $ev){
			$error_msg .= '<br>'.$ev;
		}
		$_SESSION['post_response'] = '<div class="Message"><img src="images/cross.gif"> There was an error uploading for '.$_POST['field_name'].'.'.$error_msg.'</div>';
	}

	// echo $_SERVER['SERVER_NAME'].'/admin/aa_manage.php?id='.$_POST['id'];

	header('Location: ./aa_manage.php?id='.$_POST['id']);
	exit;
}



?>