<?

$dir_path = $_SERVER['DOCUMENT_ROOT'].'/gallery/'.$_POST['field_name'];

$gallery_vars = 'Gallery__'.$_POST['field_name'];
$gallery_vars = $$gallery_vars;

require($dir_path.'/images.php');

foreach($_POST as $key => $value){
	if(preg_match('/^Current{}/', $key)){
		$key = preg_replace('/^Current{}/', '', $key);
		$imageSizes = $key.'{image}';
		if(!empty($gallery_vars[$imageSizes])){
			foreach($gallery_vars[$imageSizes] as $k => $v){
				$sizes[] = $k;
			}
		}

		if(isset($_POST['Remove{}'.$key]) && $_POST['Remove{}'.$key] == 'yes' || $_FILES[$key]['name'] != ''){

			foreach($gallery_array as $k => $v){
				if($key != $_POST['array_key'] && preg_match("/$value/", $v)){
					$skip_delete = 1;
				}
			}

			if($skip_delete != 1){
				$record_images[$key] = '';
				if(!empty($sizes)){
					foreach($sizes as $v){
						if($v == 'medium'){
							unlink($dir_path.'/'.$value);
						}
						else{
							unlink($dir_path.'/'.$v.'-'.$value);
						}
					}
				}
				else{
					unlink($dir_path.'/'.$value);				
				}
			}

		}
		else{
			$record_images[$key] = $value;
		}

	}
}

		
/***** START IMAGE RESIZER *****/

foreach($_FILES as $file_key=>$file_value){
	$source = $_FILES[$file_key]['tmp_name'];
	$target = $dir_path."/".basename( $_FILES[$file_key]['name']);
	move_uploaded_file($source, $target);

	if(isset($gallery_vars[$file_key]) && $gallery_vars[$file_key] == '{file}' && $_FILES[$file_key]['name'] != ''){
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
				
		$imagepath = $imagename;
		
		$gallery_Image_sizes = $gallery_vars[$file_key.'{image}'];
						
			
		/*****	BEGIN IMAGE RESIZE *****/
		
		$save = $dir_path."/" . $size_var . $imagepath; //This is the new file you saving
		$file = $dir_path."/" . $imagepath; //This is the original file
		
		list($width, $height) = getimagesize($file) ;
		
		if($width > 3000 || $height > 2000){
			$error = 1;
			$error_code[] = $imagename.' is too large.  Reduce size to below 3000px Width and below 2000px Height';
			break 1;
		}
				
		$depth = countdim($gallery_Image_sizes);

		pre($gallery_Image_sizes);
		
		if($depth >= 1){
			foreach($gallery_Image_sizes as $key => $value){
				if(is_array($value)){
					unset($size_var);
					if($key != 'medium'){
						$size_var = $key.'-';
					}
					
					$save = $dir_path.$size_var.$imagepath; //This is the new file you saving
					
					$resized = resize($width, $height, $value);
					$split = preg_split('/\|\|/', $resized);
					$modwidth = $split[0];
					$modheight = $split[1];

					$tn = imagecreatetruecolor($modwidth, $modheight);
					imagealphablending($tn, false);
					imagesavealpha($tn,true);
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
						imagepng($tn, $save, 0);
					}
				}
			}
		}
		elseif($gallery_Image_sizes['width'] != '' || $gallery_Image_sizes['minwidth'] != '' || $gallery_Image_sizes['maxwidth'] != '' || $gallery_Image_sizes['height'] != '' || $gallery_Image_sizes['minheight'] != '' || $gallery_Image_sizes['maxheight'] != ''){
			
			$resized = resize($width, $height, $gallery_Image_sizes);				
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
		else{
			$modwidth = $width;
			$modheight = $height;
			
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
		$record_images[$file_key] = $_FILES[$file_key]['name'];
	}
}
/***** END IMAGE RESIZER *****/

$a=0;
foreach($gallery_vars as $key => $value){
	if(!preg_match('/{.*}/', $key)){
		$get_keys[$a] = $key;
		$a++;
	}
}

$i=0;
foreach($gallery_array as $value){
	unset($find);
	if($i == $_POST['array_key']){
		$keys[$i] = '';
		foreach($gallery_vars as $key => $value){
			if(preg_match('/{image}/i', $key)){
				$image_key_name = preg_replace('/{image}/i', '', $key);
				if(isset($record_images[$image_key_name]) && $record_images[$image_key_name] == '' && !$_POST['Remove{}'.$image_key_name]){
					$record_images[$image_key_name]	= $_POST['Current{}'.$image_key_name];
				}
				$keys[$i] .= (isset($record_images[$image_key_name])) ? '||'.htmlspecialchars($record_images[$image_key_name]) : '||';
			}
			elseif($value == '{file}'){
				if($record_images[$key] == '' && !$_POST['Remove{}'.$key]){
					$record_images[$key] = $_POST['Current{}'.$key];
				}
				$keys[$i] .= (isset($record_images[$key])) ? '||'.htmlspecialchars($record_images[$key]) : '||';
			}
			elseif(preg_match('/^Type/i', $key)){
				
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
		$find = 'yes';
	}
	if(!isset($find)){
		$keys[$i] = $value;
	}
	if(isset($find)){
		$add_new = 'no';
	}
	$i++;
}

$writearray = '<?php  $gallery_array = array(';
foreach($keys as $key => $value){
	$test = explode('||', $value);
	if(!empty($test))
		$value = str_replace("'", "\'", $value);
		$writearray .= $key.' => \''.$value.'\',';
}	
$writearray .= ');  ?>';

$file = fopen($dir_path.'/images.php', "w+");
fwrite($file, $writearray);
fclose($file);	

echo '<div class="Message"><img src="images/tick.gif"> '.$gallery_vars['Type'].' updated.</div>';


?>