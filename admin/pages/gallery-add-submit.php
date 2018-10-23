<?

$gallery_vars = 'Gallery__'.$_POST['field_name'];
$gallery_vars = $$gallery_vars;

##	Defined Vars
$record_images = [];


/***** START IMAGE RESIZER *****/
foreach($_FILES as $file_key=>$file_value){
	if(isset($gallery_vars[$file_key]) && $gallery_vars[$file_key] == '{file}' && isset($_FILES[$file_key]['name']) && $_FILES[$file_key]['name'] != ''){
		$source = $_FILES[$file_key]['tmp_name'];
		$target = $_SERVER['DOCUMENT_ROOT']."/gallery/".$_POST[field_name]."/".basename( $_FILES[$file_key]['name']);
		move_uploaded_file($source, $target);		
		$record_images[$file_key] = $_FILES[$file_key]['name'];
	}
	elseif($_FILES[$file_key]['name'] != ''){
		$imagename = $_FILES[$file_key]['name'];
		$imagetype = trim(substr($imagename, strrpos($imagename, '.')));
		$imagetype = strtolower($imagetype);
		
		if($imagetype != '.jpeg' && $imagetype != '.jpg' && $imagetype != '.gif' && $imagetype != '.png'){
			$error = 1;
			$error_code[] = $imagetype.' is not a valid file type.  Convert to .jpeg, .jpg, .gif or .png and try again.';
			break 1;
		}
		
		$source = $_FILES[$file_key]['tmp_name'];
		$target = $_SERVER['DOCUMENT_ROOT']."/gallery/$_POST[field_name]/".$imagename;
		move_uploaded_file($source, $target);
		
		$imagepath = $imagename;
		
		$gallery_Image_sizes = $gallery_vars[$file_key.'{image}'];
										
		/*****	BEGIN IMAGE RESIZE *****/
		
		// $save = $_SERVER['DOCUMENT_ROOT']."/gallery/$_POST[field_name]/" . $size_var . $imagepath; //This is the new file you saving
		$file = $_SERVER['DOCUMENT_ROOT']."/gallery/$_POST[field_name]/" . $imagepath; //This is the original file
		
		list($width, $height) = getimagesize($file) ;
		
		if($width > 3000 || $height > 2000){
			$error = 1;
			$error_code[] = $imagename.' is too large.  Reduce size to below 3000px Width and below 2000px Height';
			break 1;
		}
				
		$depth = countdim($gallery_Image_sizes);
		if($depth >= 1){
			foreach($gallery_Image_sizes as $key => $value){
				if(is_array($value)){
					unset($size_var);
					unset($save);
					unset($resized);
					unset($image);
					unset($split);
					unset($modwidth);
					unset($modheight);

					$size_var = ($key != 'medium') ? $key.'-' : '';
					
					$save = $_SERVER['DOCUMENT_ROOT']."/gallery/$_POST[field_name]/" . $size_var . $imagepath; //This is the new file you saving
					
					$resized = resize($width, $height, $value);

					$split = preg_split('/\|\|/', $resized);
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

require($_SERVER['DOCUMENT_ROOT']."/gallery/$_POST[field_name]/images.php");	

$a=0;
foreach($gallery_vars as $key => $value){
	if(!preg_match('/{.*}/', $key)){
		$get_keys[$a] = $key;
		$a++;
	}
}

$keys = [];
$i=0;
if(!empty($gallery_array)){
	foreach($gallery_array as $value){
		$keys[$i] = $value;
		$i++;
	}
}

$z = 0;
foreach($gallery_vars as $key => $value){
	if(!isset($keys[$i]))
		$keys[$i] = '';
	if(preg_match('/{image}/i', $key)){
		$image_key_name = preg_replace('/{image}/i', '', $key);
		$keys[$i] .= '||';
		if(isset($record_images[$image_key_name]))
			$keys[$i] .= $record_images[$image_key_name];			
	}
	elseif($value == '{file}'){
		$keys[$i] .= '||'.$record_images[$key];
	}
	elseif(preg_match('/{select}/i', $key)){
		$key = preg_replace('/{.*}/', '', $key);
		$content = preg_replace("/'/", '&#39;', $_POST[$key]);
		$keys[$i] .= '||'.$content;	
	}
	else{
		if(isset($_POST[$key])){
			$content = preg_replace("/'/", '&#39;', $_POST[$key]);
			$content = stripslashes($content);
			$keys[$i] .= '||'.$content;
		}
		elseif(isset($_FILES[$key])){
			$content = preg_replace('/\'/', '&#39;', $_FILES[$key]['name']);
			$content = stripslashes($content);
			$keys[$i] .= '||'.$content;
		}
	}
}
$keys[$i] = substr($keys[$i], 2);

$writearray = '<?php  $gallery_array = array(';
foreach($keys as $key => $value){
	$test = explode('||', $value);
	if(!empty($test))
		$value = str_replace("'", "\'", $value);
		$writearray .= $key.' => \''.$value.'\',';
}	
$writearray .= ');  ?>';

$file = fopen($_SERVER['DOCUMENT_ROOT'].'/gallery/'.$_POST['field_name'].'/images.php', "w+");
fwrite($file, $writearray);
fclose($file);	

$show_field = preg_replace('/_/', ' ', $_POST['field_name']);

if(!isset($error)){
	echo '<div class="Message"><img src="images/tick.gif"> Your '.$show_field.' has been added.</div>';
}
else{
	foreach($error_code as $ev){
		$error_msg .= '<br>'.$ev;
	}
	echo '<div class="Message"><img src="images/cross.gif"> There was an error uploading your '.$_POST[field_name].'.'.$error_msg.'</div>';
}

?>