<?php

if(isset($_POST['page_url']))
	$url = clean_url($_POST['page_url']);
$id = $_POST['page_id'];

$info = $image_list = $groups = '';

foreach($_POST as $key => $value){
	if($key != 'page_name' && $key != 'page_url' && $key != 'submit_edit' && $key != 'page_id'){
		if(preg_match('/^Current{}/', $key)){
			$key = preg_replace('/^Current{}/', '', $key);
			if(isset($_POST['Remove{}'.$key]) && $_POST['Remove{}'.$key] != 'yes'){
				$record_images[$key] = $value;
			}
			else{
				$record_images[$key] = '';
				unlink('../images/'.$value);
			}
		}
		else{
			if(preg_match('/^\+/', $key)){
				$groupings[$key] = $value;
			}
			elseif(is_array($value)){
				$info .= '{{}}'.$key.'(())'.implode('||', $value);
			}
			else {
				$info .= '{{}}'.$key.'(())'.$value;
			}
		}
	}
}

$template = $_POST['template'];
$template_vars = $$template;

/***** START IMAGE RESIZER *****/

foreach($_FILES as $file_key=>$file_value){
	$source = $_FILES[$file_key]['tmp_name'];
	$target = "../images/".$_FILES[$file_key]['name'];

	if(isset($template_vars[$file_key]) && $template_vars[$file_key] == '{file}' && isset($_FILES[$file_key]['name']) && $_FILES[$file_key]['name'] != ''){
		move_uploaded_file($source, $target);		
		$record_images[$file_key] = $_FILES[$file_key]['name'];
	}
	elseif(isset($_FILES[$file_key]['name']) && $_FILES[$file_key]['name'] != ''){

		$imagename = $_FILES[$file_key]['name'];
		$imagetype = trim(substr($imagename, strrpos($imagename, '.')));
		$imagetype = strtolower($imagetype);
		
		if($imagetype != '.jpeg' && $imagetype != '.jpg' && $imagetype != '.gif' && $imagetype != '.png'){
			$error = 1;
			$error_code[] = $imagetype.' is not a valid file type.  Convert to .jpeg, .jpg, .gif or .png and try again.';
			break 1;
		}
		else{
			move_uploaded_file($source, $target);
		}
			
		/*****	BEGIN IMAGE RESIZE *****/
		
		// $save = "../images/" . $size_var . $imagename; //This is the new file you saving
		$file = "../images/" . $imagename; //This is the original file
					
		list($width, $height) = getimagesize($file) ;
					
		$depth = countdim($template_vars[$file_key.'{image}']);

		if($depth >= 1){
			foreach($template_vars[$file_key.'{image}'] as $key => $value){
				unset($size_var);
				unset($resized);
				unset($image);
				unset($save);
				unset($split);
				unset($modwidth);
				unset($modheight);
				if(is_array($value)){
					unset($size_var);

					$size_var = ($key != 'medium') ? $key.'-' : '';
					
					$save = "../images/" . $size_var . $imagename; //This is the new file you saving
					
					$resized = resize($width, $height, $value);
					$split = explode('||', $resized);
					$modwidth = $split[0];
					$modheight = $split[1];
					
					$tn = imagecreatetruecolor($modwidth, $modheight) ;
					if($imagetype == '.jpg' || $imagetype == '.jpeg'){
						$image = imagecreatefromjpeg($file);
						imagecopyresampled($tn, $image, 0, 0, 0, 0, $modwidth, $modheight, $width, $height);
						imagejpeg($tn, $save, 100);
					}
					elseif($imagetype == '.gif'){
						$image = imagecreatefromgif($file);
						imagecopyresampled($tn, $image, 0, 0, 0, 0, $modwidth, $modheight, $width, $height) ;
						imagegif($tn, $save, 100);
					}
					elseif($imagetype == '.png'){
						$image = imagecreatefrompng($file);
						imagecopyresampled($tn, $image, 0, 0, 0, 0, $modwidth, $modheight, $width, $height) ;
						imagepng($tn, $save, 100);
					}
				}
			}
		}
		$record_images[$file_key] = $_FILES[$file_key]['name'];
	}
}
/***** END IMAGE RESIZER *****/

if(isset($record_images)){
	foreach($record_images as $key => $value){
		$image_list .= '{{}}'.$key.'(())'.$value;
	}
}
if(isset($groupings)){
	foreach($groupings as $key => $value){
		$groups .= '{{}}'.$key.'(())'.$value;
	}
}
$info .= $image_list.$groups;
	
$mysqli->query("UPDATE $database SET info='$info' WHERE id='$id'");

$get_product = $mysqli->query("SELECT * FROM $database WHERE id='$id'");
$get_product = $get_product->fetch_array();

echo'
<div class="Title">Updated Page - '.$get_product[2].'</div>
';

$contents = explode('{{}}', $get_product[4]);
foreach($contents as $value){
	$sort = explode('(())', $value);
	$show_name = preg_replace('/_/', ' ', $sort[0]);
	$show_name = preg_replace('/\+/', ' ', $show_name);
	$show_name = ucwords($show_name);
	
	if($sort[0] == '' || $sort[0] == 'template'){
		
	}
	elseif(stristr($sort[1], '.jpg') || stristr($sort[1], '.gif') || stristr($sort[1], '.png')){
		echo'
		<div class="Label">
			'.$show_name.'
		</div>
		<div class="LabelInsert">
			<img src="../images/'.$sort[1].'">
		</div>
		';
	}
	elseif(stristr($show_name, 'Remove{}')){
		
	}
	else{
		echo'
		<div class="Label">
			'.$show_name.'
		</div>
		<div class="LabelInsert">
			'.$sort[1].'
		</div>
		';
	}
}

?>