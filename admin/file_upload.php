<?

function countdim($array){
	if (is_array(reset($array))){
		$return = countdim(reset($array)) + 1;
	}	
	else{
		$return = 1;
	}
	return $return;
}

function resize($width, $height, $value){

	if(!isset($value['width']))
		$value['width'] = '';
	if(!isset($value['height']))
		$value['height'] = '';
	if(!isset($value['minwidth']))
		$value['minwidth'] = '';
	if(!isset($value['minheight']))
		$value['minheight'] = '';

	if($value['width'] != '' && $value['height'] != ''){
		$modwidth = $value['width'];	
		$modheight = $value['height'];	
	}
	elseif($value['width'] != '' && $value['height'] == ''){
		$modwidth = $value['width'];	
		$diff = $width / $modwidth;
		$modheight = $height / $diff;
	}
	elseif($value['height'] != '' && $value['width'] == ''){
		$modheight = $value['height'];	
		$diff = $height / $modheight;
		$modwidth = $width / $diff;
	}

	if($value['minwidth'] != ''){
		$modwidth = $value['minwidth'];	
		$diff = $width / $modwidth;
		$modheight = $height / $diff;
	}
	if(isset($modheight) && $modheight < $value['minheight'] && $value['minheight'] != ''){
		$modheight = $value['minheight'];	
		$diff = $height / $modheight;
		$modwidth = $width / $diff;
	}
	
	if(!isset($modwidth) || $modwidth == ''){
		$modwidth = $width;
	}
	if(!isset($modheight) || $modheight == ''){
		$modheight = $height;
	}

	if($value['maxheight'] != '' && $value['maxheight'] < $modheight){
		$modheight = $value['maxheight'];	
		$diff = $height / $modheight;
		$modwidth = $width / $diff;
	}
	
	if($value['maxwidth'] != '' && $value['maxwidth'] < $modwidth){
		$modwidth = $value['maxwidth'];	
		$diff = $width / $modwidth;
		$modheight = $height / $diff;
	}
	
	return $modwidth.'||'.$modheight;
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
		
		$gallery_Image_sizes = $gallery_vars[$file_key.'{image}'];
						
			
		/*****	BEGIN IMAGE RESIZE *****/
		
		list($width, $height) = getimagesize($target);
		
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
					unset($split);
					unset($modwidth);
					unset($modheight);
					unset($tn);
					
					$size_var = ($key != 'medium') ? $key.'-' : '';
					
					$save = $dir_path.'/'.$size_var.$imagename; //This is the new file you saving
					
					$resized = resize($width, $height, $value);
					$split = preg_split('/\|\|/', $resized);
					$modwidth = $split[0];
					$modheight = $split[1];

					$tn = imagecreatetruecolor($modwidth, $modheight);
					imagealphablending($tn, false);
					imagesavealpha($tn, true);
					if($imagetype == '.jpg' || $imagetype == '.jpeg'){
						$image = imagecreatefromjpeg($target);
						imagecopyresampled($tn, $image, 0, 0, 0, 0, $modwidth, $modheight, $width, $height);
						imagejpeg($tn, $save, 100);
					}
					elseif($imagetype == '.gif'){
						$image = imagecreatefromgif($target);
						imagecopyresampled($tn, $image, 0, 0, 0, 0, $modwidth, $modheight, $width, $height) ;
						imagegif($tn, $save, 100);
					}
					elseif($imagetype == '.png'){
						$image = imagecreatefrompng($target);
						imagecopyresampled($tn, $image, 0, 0, 0, 0, $modwidth, $modheight, $width, $height) ;
						imagepng($tn, $save, 0);
					}
				}
			}
		}
		$record_images[$file_key] = $_FILES[$file_key]['name'];
	}
}
/***** END IMAGE RESIZER *****/


?>
