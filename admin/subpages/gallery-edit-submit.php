<?php
/** SubPage Edit Gallery Submit **/
/** SubPage Edit Gallery Submit **/
/** SubPage Edit Gallery Submit **/
/** SubPage Edit Gallery Submit **/
/** SubPage Edit Gallery Submit **/

	$gallery_vars = 'Gallery__'.$_POST[field_name];
	$gallery_vars = $$gallery_vars;


	foreach($_POST as $key => $value){
		if(preg_match('/^Current{}/', $key)){
			$key = preg_replace('/^Current{}/', '', $key);
			if($_POST['Remove{}'.$key] != 'yes'){
				$record_images[$key] = $value;
			}
			else{
				$record_images[$key] = '';
				unlink('../gallery/'.$_POST[field_name].'/'.$value);
			}
		}
	}
	
	if(in_array('{file}', $gallery_vars)){
		$key_chk = array_search('{file}', $gallery_vars);
	}	
	foreach($_FILES as $key=>$ivalue){			
		if($key_chk == $key && $_FILES[$key]['name'] != ''){
			$source = $_FILES[$key]['tmp_name'];
			$target = "../gallery/".$_POST[field_name]."/".basename( $_FILES[$key]['name']);
			move_uploaded_file($source, $target);		
			$record_images[$key] = $_FILES[$key]['name'];
		}
		elseif($_FILES[$key]['name'] != ''){
			$imagename = $_FILES[$key]['name'];
			if(preg_match('/.jpeg/', $imagename)){
				$imagetype = '.jpeg';
			}
			elseif(preg_match('/.jpg/', $imagename)){
				$imagetype = '.jpg';
			}
			elseif(preg_match('/.gif/', $imagename)){
				$imagetype = '.gif';
			}
			else{
				$error[$key] = 1;
				break 1;
			}
			$source = $_FILES[$key]['tmp_name'];
			$target = "../gallery/$_POST[field_name]/".$imagename;
			move_uploaded_file($source, $target);
			
			$imagepath = $imagename;
			
			$gallery_Image_sizes = $gallery_vars[$key.'{image}'];
			foreach($gallery_Image_sizes as $image_size_key => $image_size_value){
				
				if($image_size_key == "large" || $image_size_key == "small"){
					$size_var = $image_size_key.'-';
				}
				else{
					$size_var = '';
				}				
				
				/*****	BEGIN IMAGE RESIZE *****/
				
				$save = "../gallery/$_POST[field_name]/" . $size_var . $imagepath; //This is the new file you saving
				$file = "../gallery/$_POST[field_name]/" . $imagepath; //This is the original file
				
				list($width, $height) = getimagesize($file);
								
				if($gallery_sizing[$file_key.'{image}']['width'] != '' || $template_vars[$file_key.'{image}']['height'] != ''){
					$modwidth = $template_vars[$file_key.'{image}']['width'];
					$modheight = $template_vars[$file_key.'{image}']['height'];
					
					if($modwidth != '' && $modheight != ''){
						$modwidth = $modwidth;
						$modheight = $modheight;
					}
					elseif($modwidth == '' && $modheight != ''){
						$diff = $height / $modheight;	
						$modwidth = $width / $diff;
						$modheight = $modheight;
					}
					elseif($modwidth != '' && $modheight == ''){
						$diff = $width / $modwidth;	
						$modwidth = $modwidth;
						$modheight = $height / $diff;
					}
					else{
						$modheight = $height;
						$modwidth = $width;
					}
				}
				else{				
					$modwidth = $image_size_value['width'];
					$modheight = $image_size_value['height'];
					
					if(preg_match('/min_/', $modwidth)){
						$minwidth = preg_replace('/min_/', '', $modwidth);
											
						$diff = $width / $minwidth;
						
						$modwidth = $minwidth;
						if(preg_match('/min\_/', $modheight)){
							$minheight = preg_replace('/min_/', '', $modheight);
							$modheight = $height / $diff;
							
							if($minheight > $modheight){
								$diff = $modheight / $minheight;	
								$modwidth = $modwidth / $diff;
								$modheight = $minheight;					
							}
						}
						else{
							$modheight = $height / $diff;
						}
					}
									
					if($modwidth != '' && $modheight != ''){
						$modwidth = $modwidth;
						$modheight = $modheight;
					}
					elseif($modwidth == '' && $modheight != ''){
						$diff = $height / $modheight;	
						$modwidth = $width / $diff;
						$modheight = $modheight;
					}
					elseif($modwidth != '' && $modheight == ''){
						$diff = $width / $modwidth;	
						$modwidth = $modwidth;
						$modheight = $height / $diff;
					}
					else{
						$modheight = $height;
						$modwidth = $width;
					}
				}
				
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
			}		
			$record_images[$key] = $_FILES[$key]['name'];
		}
	}
	
	require("../gallery/$_POST[field_name]/".$_GET[y]."-images.php");	
	
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
		if($i == $_POST[gallery_id]){
			$keys[$i] = '';
			foreach($gallery_vars as $key => $value){
				if(preg_match('/{image}/i', $key)){
					$image_key_name = preg_replace('/{image}/i', '', $key);
					if($record_images[$image_key_name] == '' && !$_POST['Remove{}'.$image_key_name]){
						$record_images[$image_key_name]	= $_POST['Current{}'.$image_key_name];
					}
					$keys[$i] .= '||'.$record_images[$image_key_name];
				}
				elseif($value == '{file}'){
					if($record_images[$key] == '' && !$_POST['Remove{}'.$key]){
						$record_images[$key] = $_POST['Current{}'.$key];
					}
					$keys[$i] .= '||'.$record_images[$key];
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
			$keys[$i] = preg_replace('/^\|\|/', '', $keys[$i]);
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
		$writearray .= $key.' => \''.$value.'\',';
	}	
	$writearray .= ');  ?>';
	
	$file = fopen('../gallery/'.$_POST[field_name].'/'.$_GET[y].'-images.php', "w+");
	fwrite($file, $writearray);
	fclose($file);	

	echo '<div class="Message"><img src="images/tick.gif"> '.$gallery_vars['Type'].' updated.</div>';

?>