<?php
/** SubPage Add Gallery Submit **/
/** SubPage Add Gallery Submit **/
/** SubPage Add Gallery Submit **/
/** SubPage Add Gallery Submit **/
/** SubPage Add Gallery Submit **/

	$gallery_vars = 'Gallery__'.$_POST[field_name];
	$gallery_vars = $$gallery_vars;

	/***** START IMAGE RESIZER *****/	
	foreach($_FILES as $file_key=>$file_value){
		if($gallery_vars[$file_key] == '{file}' && $_FILES[$file_key]['name'] != ''){
			$source = $_FILES[$file_key]['tmp_name'];
			$target = "../gallery/".$_POST[field_name]."/".basename( $_FILES[$file_key]['name']);
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
			$target = "../gallery/$_POST[field_name]/".$imagename;
			move_uploaded_file($source, $target);
			
			$imagepath = $imagename;
			
			$gallery_Image_sizes = $gallery_vars[$file_key.'{image}'];
											
			/*****	BEGIN IMAGE RESIZE *****/
			
			$save = "../gallery/$_POST[field_name]/" . $size_var . $imagepath; //This is the new file you saving
			$file = "../gallery/$_POST[field_name]/" . $imagepath; //This is the original file
			
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
						if($key != 'medium'){
							$size_var = $key.'-';
						}
						
						$save = "../gallery/$_POST[field_name]/" . $size_var . $imagepath; //This is the new file you saving
						
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
	
	require("../gallery/$_POST[field_name]/".$_POST[pageid]."-images.php");	
	
	$a=0;
	foreach($gallery_vars as $key => $value){
		if(!preg_match('/{.*}/', $key)){
			$get_keys[$a] = $key;
			$a++;
		}
	}
	
	$i=0;
	foreach($gallery_array as $value){
		$split = preg_split('/\|\|/', $value);
		unset($find);
		if($_FILES['Image']['name'] == $split[0]){
			foreach($gallery_vars as $key => $value){
				if(preg_match('/{image}/i', $key)){
					$image_key_name = preg_replace('/{image}/i', '', $key);
					$keys[$i] .= '||'.$record_images[$image_key_name];
				}
				elseif(preg_match('/Type/i', $key)){
					
				}
				elseif(preg_match('/{select}/i', $key)){
					$key = preg_replace('/{.*}/', '', $key);
					$content = preg_replace("/'/", '&#39;', $_POST[$key]);
					$content = preg_replace("/\"/", '&#34;', $content);
					$keys[$i] .= '||'.$content;					
				}
				else{
					$content = preg_replace("/'/", '&#39;', $_POST[$key]);
					$content = preg_replace("/\"/", '&#34;', $content);
					$keys[$i] .= '||'.$content;
				}
			}
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
	if(!isset($add_new)){
		foreach($gallery_vars as $key => $value){
			if(preg_match('/{image}/i', $key)){
				$image_key_name = preg_replace('/{image}/i', '', $key);
				$keys[$i] .= '||'.$record_images[$image_key_name];
			}
			elseif($value == '{file}'){
				$keys[$i] .= '||'.$record_images[$key];
			}
			elseif(preg_match('/{select}/i', $key)){
				$key = preg_replace('/{.*}/', '', $key);
				$content = preg_replace("/'/", '&#39;', $_POST[$key]);
				$content = preg_replace("/\"/", '&#34;', $content);
				$keys[$i] .= '||'.$content;	
			}
			else{
				if(isset($_POST[$key])){
					$content = preg_replace('/\'/', '&#39;', $_POST[$key]);
					$content = preg_replace('/"/', '&#34;', $content);
					$content = stripslashes($content);
					$keys[$i] .= '||'.$content;
				}
				elseif(isset($_FILES[$key])){
					$content = preg_replace('/\'/', '&#39;', $_FILES[$key]['name']);
					$content = preg_replace('/"/', '&#34;', $content);
					$content = stripslashes($content);
					$keys[$i] .= '||'.$content;
				}
			}
		}
	}
	
	$writearray = '<?php  $gallery_array = array(';
	foreach($keys as $key => $value){
		$value = preg_replace('/^\|\|/', '', $value);
		$writearray .= $key.' => \''.$value.'\',';
	}	
	$writearray .= ');  ?>';
	
	$file = fopen('../gallery/'.$_POST[field_name].'/'.$_POST[pageid].'-images.php', "w+");
	fwrite($file, $writearray);
	fclose($file);	
	
	$show_field = preg_replace('/_/', ' ', $_POST[field_name]);
	
	if(!isset($error)){
		echo '<div class="Message"><img src="images/tick.gif"> Your '.$show_field.' has been added.</div>';
	}
	else{
		foreach($error_code as $ev){
			$error_msg .= '<br>'.$ev;
		}
		echo '<div class="Message"><img src="images/cross.gif"> There was an error uploading your '.$show_field.'.'.$error_msg.'</div>';
	}
?>