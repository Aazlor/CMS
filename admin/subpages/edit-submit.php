<?php
/** SubPage Edit Page Submit **/
/** SubPage Edit Page Submit **/
/** SubPage Edit Page Submit **/
/** SubPage Edit Page Submit **/
/** SubPage Edit Page Submit **/


	$url = clean_url($_POST[Page_Name]);
	$id = $_POST['page_id'];
	$parent = $_POST['parent'];

	$parent_vars = $mysqli->query("SELECT * FROM $database WHERE id='$parent'");
	$parent_vars = mysql_fetch_row($parent_vars);
	
	$var_list = preg_replace('/ /', '', $parent_vars[2]);
	$var_list = preg_replace('/\'/', '', $var_list);
	$var_list = 'Sub_'.$var_list;
	$template_vars = $$var_list;
		
	$field_name = array_search('{photogallery}', $template_vars);


	foreach($_POST as $key => $value){
		if($key != 'page_name' && $key != 'page_url' && $key != 'submit_edit' && $key != 'page_id'){
			if(preg_match('/^Current{}/', $key)){
				$key = preg_replace('/^Current{}/', '', $key);
				if($_POST['Remove{}'.$key] != 'yes'){
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
				else {
					$info .= '{{}}'.$key.'(())'.$value;
				}
			}
		}
	}

	
	/***** START IMAGE RESIZER *****/

	foreach($_FILES as $file_key=>$file_value){
		if($template_vars[$file_key] == '{file}' && $_FILES[$file_key]['name'] != ''){
			$source = $_FILES[$file_key]['tmp_name'];
			$target = "../files/".$_FILES[$file_key]['name'];
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
			$target = "../images/".$imagename;
			move_uploaded_file($source, $target);
			
			$imagepath = $imagename;
							
				
			/*****	BEGIN IMAGE RESIZE *****/
			
			$save = "../images/" . $size_var . $imagepath; //This is the new file you saving
			$file = "../images/" . $imagepath; //This is the original file
						
			list($width, $height) = getimagesize($file) ;
			
// 			if($width > 3000 || $height > 2000){
// 				$error = 1;
// 				$error_code[] = $imagename.' is too large.  Reduce size to below 3000px Width and below 2000px Height';
// 				break 1;
// 			}
			
			$depth = countdim($template_vars[$file_key.'{image}']);
			
			if($depth >= 1){
				foreach($template_vars[$file_key.'{image}'] as $key => $value){
					if(is_array($value)){
						unset($size_var);
						if($key != 'medium'){
							$size_var = $key.'-';
						}
						
						$save = "../images/" . $size_var . $imagepath; //This is the new file you saving
						
						$resized = resize($width, $height, $template_vars[$file_key.'{image}']);

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
			elseif($template_vars[$file_key.'{image}']['width'] != '' || $template_vars[$file_key.'{image}']['minwidth'] != '' || $template_vars[$file_key.'{image}']['maxwidth'] != '' || $template_vars[$file_key.'{image}']['height'] != '' || $template_vars[$file_key.'{image}']['minheight'] != '' || $template_vars[$file_key.'{image}']['maxheight'] != ''){
				
				$resized = resize($width, $height, $template_vars[$file_key.'{image}']);
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
			
			if(preg_match('/^\+/', $file_key)){
				$groupings[$file_key] = $_FILES[$file_key]['name'];
			}
			else{
				$record_images[$file_key] = $_FILES[$file_key]['name'];
			}
		}
	}

	/***** END IMAGE RESIZER *****/
	
	foreach($record_images as $key => $value){
		$image_list .= '{{}}'.$key.'(())'.$value;
	}
	foreach($groupings as $key => $value){
		$groups .= '{{}}'.$key.'(())'.$value;
	}
	$info .= $image_list.$groups;
	
	$info = preg_replace('/^{{}}/', '', $info);

	$mysqli->query("UPDATE $database SET name='$_POST[Page_Name]', relation='$url', info='$info' WHERE id='$id'");
	
	$get_product = $mysqli->query("SELECT * FROM $database WHERE id='$id' ORDER BY name ASC");
	$get_product = mysql_fetch_row($get_product);
	
	echo'
	<div class="Title">Updated '.$parent_vars[2].' - '.$get_product[2].'</div>
	';
	
	$contents = preg_split('/{{}}/', $get_product[4]);
	foreach($contents as $value){
		$sort = preg_split('/\(\(\)\)/', $value);
		$show_name = preg_replace('/_/', ' ', $sort[0]);
		$show_name = preg_replace('/\+/', ' ', $show_name);
		$show_name = preg_replace('/{}/', ' ', $show_name);
		$show_name = ucwords($show_name);
		
		if($sort[0] == '' || $sort[0] == 'template' || $sort[0] == 'parent'){
			
		}
		elseif(preg_match('/\.jpg/', $sort[1]) || preg_match('/\.gif/', $sort[1]) || preg_match('/\.png/', $sort[1])){
			echo'
			<div class="Label">
				'.$show_name.'
			</div>
			<div class="LabelInsert">
				<img src="../images/'.$sort[1].'">
			</div>
			';
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