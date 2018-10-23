<?php
/** SubPage Add Page Submit **/
/** SubPage Add Page Submit **/
/** SubPage Add Page Submit **/
/** SubPage Add Page Submit **/
/** SubPage Add Page Submit **/

	$url = clean_url($_POST[Page_Name]);
	$id = $_POST['page_id'];
	$parent = $_POST['parent'];
	
	$parent_vars = $mysqli->query("SELECT * FROM $database WHERE id='$parent'");
	$parent_vars = mysql_fetch_row($parent_vars);
	
	$var_list = preg_replace('/[\s]+/', '', $parent_vars[2]);
	$var_list = preg_replace('/[\W]+/', '', $var_list);
	$var_list = 'Sub_'.$var_list;

	$template_vars = $$var_list;
		
	/***** START IMAGE RESIZER *****/
	
	foreach($_FILES as $file_key=>$file_value){
		if($_FILES[$file_key]['name'] != ''){
			$imagename = $_FILES[$file_key]['name'];
			if(preg_match('/.jpeg/i', $imagename)){
				$imagetype = '.jpeg';
			}
			elseif(preg_match('/.jpg/i', $imagename)){
				$imagetype = '.jpg';
			}
			elseif(preg_match('/.gif/i', $imagename)){
				$imagetype = '.gif';
			}
			else{
				$error = 1;
				echo '____FAIL____';
				break 1;
			}
			
			$source = $_FILES[$file_key]['tmp_name'];
			$target = "../images/".$imagename;
			move_uploaded_file($source, $target);
			
			$imagepath = $imagename;
			
			foreach($template_vars as $k => $v){
				$check = $file_key;
				if(preg_match("/$check/i", $k)){
					$page_image_sizes = $v;
				}
			}
			
			foreach($page_image_sizes as $image_size_key => $image_size_value){
				
				if($image_size_key != "medium"){
					$size_var = $image_size_key.'-';
				}
				else{
					$size_var = '';
				}
				
				
				/*****	BEGIN IMAGE RESIZE *****/
				
				$save = "../images/" . $size_var . $imagepath; //This is the new file you saving
				$file = "../images/" . $imagepath; //This is the original file
				
				list($width, $height) = getimagesize($file) ;
				
				if($template_vars[$file_key.'{image}']['width'] != '' || $template_vars[$file_key.'{image}']['height'] != ''){
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
			
			if(preg_match('/^\+/', $file_key)){
				$groupings[$file_key] = $_FILES[$file_key]['name'];
			}
			else{
				$record_images[$file_key] = $_FILES[$file_key]['name'];
			}
		}
	}
	/***** END IMAGE RESIZER *****/
	
	foreach($_FILES as $key => $value){
		$content .= '{{}}'.$key.'(())'.$value['name'];
	}
	foreach($_POST as $key => $value){
		if($key != 'Page_Name' && $key != 'submit_add'){
			$content .= '{{}}'.$key.'(())'.$value;
		}
	}
	$content = preg_replace('/^{{}}/', '', $content);

	$mysqli->query("INSERT INTO $database (type, name, relation, info) VALUES ('$type', '$_POST[Page_Name]', '$url', '$content')");
	$get_id = $mysqli->query("SELECT * FROM $database ORDER BY id DESC LIMIT 1");
	$get_id = mysql_fetch_row($get_id);
	
	foreach($template_vars as $key => $value){
		if($value == '{photogallery}'){
			$ourFileName = '../photogallery/'.$key.'/'.$get_id[0].'-images.php';
			$ourFileHandle = fopen($ourFileName, 'x+') or die("can't open file");
			fclose($ourFileHandle);			
		}
	}
	
	$title = stripslashes($_POST[Page_Name]);
	echo'
	<div class="Title">
			'.$title.' - Page Has Been Added
	</div>
	';

?>